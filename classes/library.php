<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of library
 *
 * @author sun2
 */
class Library {
    //put your code here
    private $path;
    private $title_list = array();
    private $titles = array();
    
    public function __construct($path=PLEX_LIB) {
        if (!is_dir($path) || !is_readable($path) || !is_writable($path)) {
            exit("Library at $path unavailable! Incorrect path!");
        }
        $this->path=$path;
        $this->loadLibrary();
    }
    public function getTitleList() {
        return $this->title_list;
    }
    
    public function hasTitle($name="") {
        if (in_array($name,$this->title_list)) {
            return true;
        } else return false;
    }
    
    private function titleGetId($title_name) {
       $id=-1;
        foreach ($this->titles as $t_id=>$title) {
          $name=$title->getName();
          if ($name == $title_name) {
              $id=$t_id;
              break;
          }
        } 
        return $id;
    }
    
    public function titleHasSeason($title_name,$season) {
       $result = false;
       if ($this->loadTitle($title_name)) {
        $id = $this->titleGetId($title_name);
        if ($this->titles[$id]->hasSeason($season)) {
            $result=true;
        }
       } else {
           exit("Title $title_name couldn't be loaded - no such folder.");
       }
       return $result;
    }
    
    public function loadLibrary() {
        $result=true;
        $lib_folder = new Folder($this->path);
        $this->title_list = $lib_folder->getSubfolders();
        foreach ($this->title_list as $name) {
            $result=$result and $this->loadTitle($name);
        }
        return $result;
    }
    
    public function loadTitle($title_name) {
        if (!in_array($title_name,$this->title_list)) {
            return false;
        }
        //Проверка на то, был ли тайтл загружен
        $id = $this->titleGetId($title_name);
        if ($id<0) {
        //
        $title = new Title();
        $title->setName($title_name);
        $title_folder = new Folder($this->path."/".$title_name);
        //Ищем сезоны
        $title_subfolders = $title_folder->getSubfolders();
        foreach ($title_subfolders as $folder) {
            if (strpos($folder,'eason')) {
               $num=intval(str_replace("Season ","",$folder));
               if ($num>0) {
                   $title->createSeason($num);
                   //Загружаем эпизоды
                   $folder=$this->path."/".$title_name."/".$folder;
                   $media_folder = new Folder($folder);
                   $vids=$media_folder->getVideos();
                   $subs=$media_folder->getSubs();
                   $auds=$media_folder->getAudios();
                   if (!empty($vids)) {
                       $cwd=getcwd();
                       chdir($folder);
                       foreach ($vids as $vid) {
                          $link=readlink($vid['name']);
                          if(!empty($link)) {
                              $episode = new Episode($link);
                              //Загружаем симлинки субтитров
                              if(!empty($subs)) {
                                  foreach ($subs as $file) {
                                      $link=readlink($file['name']);
                                      if(!empty($link)) {
                                          $link_info=pathinfo($link);
                                          $ttl=explode(".",$file['title']);
                                          if ($ttl[0] == $vid['title']) {
                                              $episode->addSub($link, $link_info['dirname']);
                                          }
                                          if ($ttl[1] == "ru") {
                                              $episode->setPriorSub($link_info['dirname'],0);
                                          }
                                          if ($ttl[1] === "rus") {
                                              $episode->setPriorSub($link_info['dirname'],1);
                                          }
                                      }
                                  }
                              }
                              //Загружаем симлинки аудио
                               if(!empty($auds)) {
                                  foreach ($auds as $file) {
                                      $link=readlink($file['name']);
                                      if(!empty($link)) {
                                          $link_info=pathinfo($link);
                                          $ttl=explode(".",$file['title']);
                                          if ($ttl[0] == $vid['title']) {
                                              $episode->addAudio($link, $link_info['dirname']);
                                          }
                                          if ($ttl[1] == "ru") {
                                              $episode->setPriorAudio($link_info['dirname'],0);
                                          }
                                          if ($ttl[1] === "rus") {
                                              $episode->setPriorAudio($link_info['dirname'],1);
                                          }
                                      }
                                  }
                              }
                              //Добавляем эпизод
                              $id=0;
                              $id=intval(preg_replace('/^.* - s..e/','',$vid['title']));
                              $title->addEpisode($num,$episode,$id);
                              //
                          }    
                       }
                    chdir($cwd);   
                   }
                   //
               }
            }
            
        }
            $this->titles[]=$title;    
        }
        return true;
    }
    
    public function addTitle($title) {
        if (!empty($title)) {
           $name=$title->getName();
           if (!$this->hasTitle($name)) {
               $this->titles[]=$title;
               $this->title_list[]=$name;
           } else {
               $this->titles[$this->titleGetId($name)]=$title; //Заменяем
           }
           return true;
        } else {
            return false;
        }
    }
    private function numstr($num) {
        $str="";
        if ($num<10) {
                $str.="0".$num;
        } else {
                $str.="$num"; 
        }
        return $str;
    }
    private function calcSymlinkPath($target_path,$link_dir) { //Путь для относительного симлинка
        if (preg_match("/^\.\./",$target_path)) { //Если путь уже относительный, проверяем его валидность
                 $cwd=  getcwd();
                 chdir($link_dir);
                 if (!is_readable($target_path)) {
                    error_log("PlexLibManager: wrong symlink data!");
                    return "";
                 } else {
                 chdir($cwd);
                 return $target_path;
                 }
        }
        $prefix="";
        $rs_path=preg_replace("/^./","",$link_dir); //Убираем начало строки
        $i=0;
        while (!empty($rs_path) && !strpos($target_path,$rs_path) && $i<MAXDEPTH) { //i - глубина вложений
            $rs_path=preg_replace("/\/?[^\/]*$/","",$rs_path); //Сокращаем путь
            $prefix.="../";
            $i++;
        }
        if ($i==MAXDEPTH) {
            error_log("PlexLibManager: Depth $i reached and can't calc symlink path, check your config and folder structure!");
            return "";
        }
        $re_path=str_replace("$rs_path","",$target_path);
        $re_path=$prefix.$re_path;
        $re_path=preg_replace("/\/{2,5}/","/",$re_path); //Лишние слэши
        return $re_path;
    }
    
    private function writeTitle($id) {
        $result=true;
        $title=$this->titles[$id];
        //Записываем данные тайтла на диск
        $name=$title->getName();
        $path=$this->path."/".$name;
        if (!is_dir($path)) {
            mkdir($path); //Создаем папку для тайтла
            ///$log->write("mkdir $path");
        } 
        $seasons=$title->getSeasons();
        foreach ($seasons as $season) {
            $s_num=$this->numstr($season->getNumber());
            $s_path=$path."/Season ".$s_num;
            if (is_dir($s_path)) {
               $this->deldir($s_path); //Удаляем старые данные сезона
                ///log->write("rmdir $s_path");
            }
            mkdir($s_path); //Создаем папку сезона
            ///$log->write("mkdir $s_path");
            $episodes=$season->getEpisodes();
            $cwd=getcwd();
            chdir($s_path); //Переходим в папку сезона
            foreach ($episodes as $id=>$episode) {
                $basename=$name." - s".$s_num."e".$this->numstr($id);
                $e_path=$episode->getPath();
                $pathinfo=pathinfo($e_path);
                $re_path=$this->calcSymlinkPath($e_path, $s_path); //Получаем путь симлинка
                symlink($re_path,$basename.'.'.$pathinfo['extension']);
                //Subs
                $subs=$episode->getSubs();
                foreach ($subs as $id=>$file) {
                    $pathinfo=pathinfo($file['path']);
                    $substr="rus".$id;
                    if ($id == 0) {
                        $substr="ru";
                    } elseif ($id == 1) {
                        $substr="rus";
                    }
                    $rs_path=$this->calcSymlinkPath($file['path'], $s_path);
                    symlink($rs_path,$basename.".".$substr.".".$pathinfo['extension']); //Создаем симлинк на сабы
                }
                //Audio
                $auds=$episode->getAud();
                foreach ($auds as $id=>$file) {
                    $pathinfo=pathinfo($file['path']);
                    $substr="rus".$id;
                    if ($id == 0) {
                        $substr="ru";
                    } elseif ($id == 1) {
                        $substr="rus";
                    }
                    $rs_path=$this->calcSymlinkPath($file['path'], $s_path);
                    symlink($rs_path,$basename.".".$substr.".".$pathinfo['extension']); //Создаем симлинк на озвучку
                }
            }
            chdir($cwd);
        }
        //
        return $result;
    }
    
    public function Save($title_name="") {
        if (!empty($title_name) && $this->hasTitle($title_name)) {
            if(!$this->writeTitle($this->titleGetId($title_name))) {
                return false;
            }
            $this->loadTitle($title_name);
            return true;
        } elseif (empty($title_name)) {
            //Очищаем папку библиотеки
            foreach ($this->title_list as $folder) {
                $this->deldir($this->path."/".$folder);
            }
            foreach ($this->titles as $title) {
                $name=$title->getName();
                if(!$this->writeTitle($this->titleGetId($name))) {
                    return false;
                }
            }
            $this->loadLibrary();
            return true;
        } 
        return false;
    }
    public function getTitle($title_name) {
        $title=array();
        if ($this->hasTitle($title_name)) {
            $this->loadTitle($title_name);
            $title=$this->titles[$this->titleGetId($title_name)];
        }
        return $title;
    }
    public function getTitles() {
        return $this->titles;
    }
    public function delTitle($title_name) {
        if ($this->hasTitle($title_name)) {
            unset($this->titles[$this->titleGetId($title_name)]);
            $this->Save();
            return true;
        } else {
            return false;
        }
    }
    public function delTitleSeason($title_name, $s_id) {
        if ($this->hasTitle($title_name)) {
            if ($this->titleHasSeason($title_name, $s_id)) {
                if($this->titles[$this->titleGetId($title_name)]->removeSeason($s_id)) {
                    //Если тайтл стал пустым
                    if (!$this->titles[$this->titleGetId($title_name)]->seasonsCount()) {
                       unset($this->titles[$this->titleGetId($title_name)]); 
                    }
                    //
                    $this->Save();
                    return true;
                }
            }    
        } 
        return false;
    }
    private function deldir($dir){ 
        $d=opendir($dir); 
        if($d) {
        while(($entry=readdir($d))!==false) {
            if ($entry != "." && $entry != "..") {
                if (is_dir($dir."/".$entry)) { 
                    $this->deldir($dir."/".$entry); 
                } else { 
                    unlink ($dir."/".$entry); 
                }
            }
        }
        closedir($d); 
        rmdir ($dir); 
        }
    } 
}
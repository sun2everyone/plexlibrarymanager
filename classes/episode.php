<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of episode
 *
 * @author sun2
 */
class Episode {
    //put your code here
    private $vid;
    private $name;
    private $sub = array();
    private $aud = array();
    
    public function __construct($path, $name="") {
        if (!is_file($path) || !is_readable($path)) {
            exit("File $path unavailable! Incorrect path!");
        }
        $this->vid=$path;
        $this->name=$name;
    }
    public function addSub($path,$dir) {
       if (!is_file($path) || !is_readable($path)) {
            return false;
        } else {
            foreach ($this->sub as $entry) {
                if ($entry['path']==$path) {
                    return 0;
                }
            }
            $sub=array(
                "path" => $path,
                "name" => $dir,
            );
            $this->sub[]=$sub;
        }
    }
    public function addAudio($path,$dir) {
        if (!is_file($path) || !is_readable($path)) {
            return false;
        } else {
            foreach ($this->aud as $entry) {
                if ($entry['path']==$path) {
                    return 0;
                }
            }
            $aud=array(
                "path" => $path,
                "name" => $dir,
            );
            $this->aud[]=$aud;
        }
    }
    public function setPriorSub($dir_name,$p_id=0) { //Предпочитаемые внешние субтитры
        if(!empty($dir_name) && !empty($this->sub)) {
            if ($p_id > (count($this->sub)-1)) {
                $p_id=count($this->sub)-1;
            }
            foreach ($this->sub as $id=>$sub) {
                if ($sub['name']==$dir_name && ($id<>$p_id)) {
                   $tmp=$this->sub[$p_id]; 
                   $this->sub[$p_id]=$sub;
                   $this->sub[$id]=$tmp;
                   break;
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function setPriorAudio($dir_name,$p_id=0) { //Предпочитаемая внешняя озвучка
        if(!empty($dir_name) && !empty($this->aud)) {
            foreach ($this->aud as $id=>$aud) {
                if ($aud['name']==$dir_name && $id<>$p_id) {
                   $tmp=$this->aud[$p_id]; 
                   $this->aud[$p_id]=$aud;
                   $this->aud[$id]=$tmp;
                   break;
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name=$name; 
    }
    public function getPath() {
        return $this->vid;
    }
    public function getSubs() {
        return $this->sub;
    }
    public function getAud() {
        return $this->aud;
    }
    
}

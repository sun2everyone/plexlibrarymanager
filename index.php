<?php
/*written by sun2everyone*/
ini_set("display_errors","on"); //Отладочная опция
ini_set("max_execution_time","10"); //Отладочная опция
function dump($msg) {
   echo '<pre>';
   print_r($msg);
   echo '</pre>';
}
require 'config.php';
require 'auth.php';
require 'log.php';
require 'template.php';
require 'classes/folder.php';
require 'classes/episode.php';
require 'classes/season.php';
require 'classes/title.php';
require 'classes/library.php';
require 'url.php';
require 'functions.php';

authenticate();
$auth_user = $_SERVER['PHP_AUTH_USER'];
$post = filter_input_array(INPUT_POST);
$get = filter_input_array(INPUT_GET);

//Check configuration
if (!$configured) {
    echo "Before use you should adjust settings in config.php!";
    exit();
}

//Initialization
$url = new url();
$log = new Log();
$tpl = new Template();
$data = array();
$root_media = new Folder(SRC_FOLDER);
$library = new Library(PLEX_LIB);
$library->loadLibrary();
//dump($library);
$ajax=0;

//Data
$data['auth_user'] = $auth_user;
$data['hostname'] = HOSTNAME;
$data['src_root_path'] = SRC_FOLDER;
$data['plex_root_path'] = PLEX_LIB;
$data['root_folder'] = $root_media->getFolder();

//AJAX
//Получение дерева папок
if (isset($post['getfolder'])) {
    $ajax = 1;
    $json = array();
    if (!empty($post['getfolder'])) {
       $folder = new Folder($post['getfolder']);
       if ($folder) {
       $json['folder']=$folder->getFolder();
        } else {
          $json['error'] ="Error trying to get folder contents!";
        }
    } else {
        $json['error'] = 'Cannot get folder contents - path empty!';
    }
    echo json_encode($json);
}
//Проверка данных формы тайтла
if (isset($get['action']) && ($get['action'] == "validate_title")) {
    $ajax = 1;
    $json = array();
    if (isset($post['title_data']) && !empty($post['title_data'])) {
        $title_data=$post['title_data'];
        $json=validateTitleData($title_data,$library);
    } else {
        $json['error'] = 'Данные тайтла не получены!';
    }
    if (!isset($json['error']) && !isset($json['warning'])) {
        $json['status']="valid";
    }
    echo json_encode($json);
}
//Добавление тайтла в библиотеку
if (isset($get['action']) && ($get['action'] == "validate_title_submit")) {
    $ajax = 1;
    ini_set("display_errors","off");
    $json = array();
    if (isset($post['title_data']) && !empty($post['title_data'])) {
        $title_data=$post['title_data'];
        $json=validateTitleData($title_data,$library);
        if (!isset($json['error'])) { 
            //Проверка пройдена, создаем структуру тайтла
            $title_name=trim($title_data['name']);
            if ($library->hasTitle($title_name)) {
                $title=$library->getTitle($title_name); //Если тайтл есть, подгружаем его из библиотеки
            } else{
              $title=new Title();
            }
             $title->setName($title_name);
             $title->createSeason($title_data['season']);
             foreach ($title_data['episodes'] as $episode_data) {
                 $episode=new Episode($episode_data['path']);
                 $episode->setName($episode_data['id']);
                 if(isset($episode_data['sub']) && !empty($episode_data['sub'])) {
                     foreach ($episode_data['sub'] as $sub) {
                         $episode->addSub($sub['path'], $sub['name']);
                     }
                     $episode->setPriorSub($title_data['pref_folder_sub']);
                 }  
                 if(isset($episode_data['aud']) && !empty($episode_data['aud'])) {
                     foreach ($episode_data['aud'] as $aud) {
                         $episode->addAudio($aud['path'], $aud['name']);
                     }
                     $episode->setPriorAudio($title_data['pref_folder_sub']);
                 }
                 $title->addEpisode($title_data['season'], $episode, $episode_data['id']);
             }
            //Добавляем тайтл в библиотеку
            if ($library->addTitle($title)) {
                //И записываем изменения на диск
                if($library->Save($title_name)) {
                    $json['status']="Тайтл $title_name (сезон ".$title_data['season'].") успешно сохранен в библиотеку.";
                    unset($json['warning']); 
                } else {
                    $json['error']="Не удалось сохранить библиотеку.";
                }
            } else {
                $json['error']="Не удалось добавить тайтл в библиотеку.";
            }
            
        }
    } else {
        $json['error'] = 'Данные тайтла не получены!';
    }
    echo json_encode($json);
}
//AJAX END

//Main cycle
if (!isset($get['mode']) || empty($get['mode'])) {
    $data['mode']="view";
} else {
    $data['mode']=$get['mode'];
}
if ($data['mode'] == "edit") { //Редактирование тайтла/сезона
    /*
    if(isset($post['title_name']) && !empty($title_name)) {
        if (isset($post['season']) && $post['season']>0) {
            //Загрузка формы для редактирования тайтла
            
            //
        } else {
           $data['error']="Невозможно отредактировать - нет такого сезона."; 
        }
    } else {
        $data['error']="Невозможно отредактировать - неверное название тайтла.";
    }
     * 
     */
    $data['warning']="Функция редактирования еще не написана. Можно удалить тайтл/сезон или просто добавить его заново :)";
    $data['mode']="view";
} elseif ($data['mode'] == "del") { //Удаление тайтла/сезона
    if(isset($post['title_name']) && !empty($post['title_name'])) {
        if (isset($post['season']) && $post['season']>0) {
            //Загрузка формы для редактирования тайтла
            if(!$library->delTitleSeason($post['title_name'],$post['season'])) {
                 $data['error']="Невозможно удалить - нет такого сезона.";
            } 
            //
        } elseif (!isset($post['season']) || ($post['season']=='0')) {
           if(!$library->delTitle($post['title_name'])) {
                 $data['error']="Невозможно удалить - нет такого тайтла.";
            }
        }
    } else {
        $data['error']="Невозможно удалить - неверное название тайтла.";
    }
    if(!isset($data['error'])) {
        $data['status']="Удаление успешно.";
        $data['mode']="view";
    }
} elseif ($data['mode'] == "parse") { //Парсинг директории с аниме-сериалом
  if (isset($post['src_folder_media']) && !empty($post['src_folder_media'])) {
      $data['src_folder_media']=$post['src_folder_media'];  
      $title_data=parseAnime($post['src_folder_media'],isset($post['src_folder_sub']) ? $post['src_folder_sub'] : "",isset($post['src_folder_audio']) ? $post['src_folder_audio'] : "");
      if ($title_data) {
          $title=array();
          //Вывод данных парсинга в поля формы
          $title['name']=$title_data->getName();
          $title['sub_folders']=$title_data->getSub_folders(1);
          $title['aud_folders']=$title_data->getAud_folders(1);
          $episodes=$title_data->getEpisodes(1);
          $title['episodes']=array();
          $k=0;
          foreach ($episodes as $episode) {
            $id=guessEpisodeNumber($episode->getName());
            $title['episodes'][$k]['name']=$episode->getName();
            $title['episodes'][$k]['path']=$episode->getPath();
            $title['episodes'][$k]['sub']=$episode->getSubs();
            $title['episodes'][$k]['aud']=$episode->getAud();
            $title['episodes'][$k]['id']=$id;
            $k++;
          }
          $data['title_data']=$title;
      } else {
           $data['error']="Не удалось найти видеофайлы в директории ".$post['src_folder_media']."!";
            $data['mode']="add";
      }
  }  else {
      $data['error']="Неверно указана дирекотория с видео!";
      $data['mode']="add";
  }
} 
if ($data['mode'] == "view") { //Просмотр библиотеки
      $data['titles']=array();
      $titles=$library->getTitles();  
      if(!empty($titles)) {
          foreach ($titles as $key=>$title) {
            $data['titles'][$key]['name']=$title->getName(); 
            $seasons=$title->getSeasons();
            foreach ($seasons as $id=>$season) {
               $data['titles'][$key]['seasons'][$id]=$season->episodesCount();//Подгружаем число эпизодов 
            }
          }
      }
}

//Main Template
if (!$ajax) {
$tpl->data = $data;
$html = $tpl->fetch('template.html');
echo $html;
}

?>
<?php
/*
 * written by sun2everyone@gmail.com
 * 2017
 *  */
//Debug functions
ini_set("display_errors","on"); 
ini_set("max_execution_time","10"); 
function dump($msg) {
   echo '<pre>';
   print_r($msg);
   echo '</pre>';
}
//

if (is_file('config.php')) {
    require 'config.php';
    $configured=1;
} else {
    require 'config_base.php';
    $configured=0;
}
require "lang/$lang.php";
require 'auth.php';
require 'log.php';
require 'template.php';
require 'classes/folder.php';
require 'classes/episode.php';
require 'classes/season.php';
require 'classes/title.php';
require 'classes/library.php';
require 'functions.php';


if($require_authentication) {
authenticate();
$auth_user = $_SERVER['PHP_AUTH_USER'];
}
$post = filter_input_array(INPUT_POST);
$get = filter_input_array(INPUT_GET);

//Check configuration
if (!$configured) {
    echo $strings['unconfigured'];
    exit();
}

//Initialization
$log = new Log();
$tpl = new Template();
$data = array();
$root_media = new Folder(SRC_FOLDER);
$library = new Library(PLEX_LIB);
$library->loadLibrary();
$ajax=0;

//Data (main template array)
isset($auth_user) ? $data['auth_user'] = $auth_user : $data['auth_user']="Anonymous";
$data['strings']=$strings;
$data['hostname'] = HOSTNAME;
$data['src_root_path'] = SRC_FOLDER;
$data['plex_root_path'] = PLEX_LIB;
$data['root_folder'] = $root_media->getFolder();

//AJAX functions////////////////////////////////////////////////////////////////
//Getting folder tree
if (isset($post['getfolder'])) {
    $ajax = 1;
    $json = array();
    if (!empty($post['getfolder'])) {
       $folder = new Folder($post['getfolder']);
       if ($folder) {
       $json['folder']=$folder->getFolder();
        } else {
          $json['error'] =$strings['err_folder_contents'];
        }
    } else {
        $json['error'] = $strings['err_empty_path'];
    }
    echo json_encode($json);
}
//Validating title form data
if (isset($get['action']) && ($get['action'] == "validate_title")) {
    $ajax = 1;
    $json = array();
    if (isset($post['title_data']) && !empty($post['title_data'])) {
        $title_data=$post['title_data'];
        $json=validateTitleData($title_data,$library);
    } else {
        $json['error'] = $strings['err_title_data'];
    }
    if (!isset($json['error']) && !isset($json['warning'])) {
        $json['status']="valid";
    }
    echo json_encode($json);
}
//Adding title to library
if (isset($get['action']) && ($get['action'] == "validate_title_submit")) {
    $ajax = 1;
    ini_set("display_errors","off");
    $json = array();
    if (isset($post['title_data']) && !empty($post['title_data'])) {
        $title_data=$post['title_data'];
        $json=validateTitleData($title_data,$library);
        if (!isset($json['error'])) { 
            //Creating title structure
            $title_name=trim($title_data['name']);
            if ($library->hasTitle($title_name)) {
                //If title exists, loading it from library
                $title=$library->getTitle($title_name); 
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
            //Adding title to library
            if ($library->addTitle($title)) {
                //And writinh
                if($library->Save($title_name)) {
                    $json['status']=sprintf($strings['title_add_success'],$title_name,$title_data['season']);
                    unset($json['warning']); 
                } else {
                    $json['error']=$strings['err_lib_save'];
                }
            } else {
                $json['error']=$strings['err_title_add'];
            }
            
        }
    } else {
        $json['error'] = $strings['err_title_data'];
    }
    echo json_encode($json);
}
//AJAX END//////////////////////////////////////////////////////////////////////

//MAIN CYCLE////////////////////////////////////////////////////////////////////
if (!isset($get['mode']) || empty($get['mode'])) {
    $data['mode']="view";
} else {
    $data['mode']=$get['mode'];
}
if ($data['mode'] == "edit") { //Title/season editing
    
    $data['warning']=$strings['no_edit_function'];
    $data['mode']="view";
} elseif ($data['mode'] == "del") { //Deleting title/season
    if(isset($post['title_name']) && !empty($post['title_name'])) {
        if (isset($post['season']) && $post['season']>0) {
            if(!$library->delTitleSeason($post['title_name'],$post['season'])) {
                 $data['error']=$strings['err_no_season'];
            } 
            //
        } elseif (!isset($post['season']) || ($post['season']=='0')) {
           if(!$library->delTitle($post['title_name'])) {
                 $data['error']=$strings['err_no_title'];
            }
        }
    } else {
        $data['error']=$strings['err_title_del_name'];
    }
    if(!isset($data['error'])) {
        $data['status']=$strings['del_success'];
        $data['mode']="view";
    }
} elseif ($data['mode'] == "parse") { //Parsing source media folder direcroty
  if (isset($post['src_folder_media']) && !empty($post['src_folder_media'])) {
      $data['src_folder_media']=$post['src_folder_media'];  
      $title_data=parseAnime($post['src_folder_media'],isset($post['src_folder_sub']) ? $post['src_folder_sub'] : "",isset($post['src_folder_audio']) ? $post['src_folder_audio'] : "");
      if ($title_data) {
          $title=array();
          //Output parsing data into form
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
           $data['error']=sprintf($strings['err_no_vid_in_dir'],$post['src_folder_media']); 
            $data['mode']="add";
      }
  }  else {
      $data['error']=$strings['err_vid_dir'];
      $data['mode']="add";
  }
} 
if ($data['mode'] == "view") { //View library
      $data['titles']=array();
      $titles=$library->getTitles();  
      if(!empty($titles)) {
          foreach ($titles as $key=>$title) {
            $data['titles'][$key]['name']=$title->getName(); 
            $seasons=$title->getSeasons();
            foreach ($seasons as $id=>$season) {
               //Loading episodes count for season
               $data['titles'][$key]['seasons'][$id]=$season->episodesCount(); 
            }
          }
      }
}

//Main Template output//////////////////////////////////////////////////////////
if (!$ajax) {
$tpl->data = $data;
$html = $tpl->fetch('template.html');
echo $html;
}

?>
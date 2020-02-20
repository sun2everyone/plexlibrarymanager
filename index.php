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
    if (CONF_V == 2) {
        $configured=1;
    } else {
        require 'config_base.php';
        $configured=0;
    }
} else {
    require 'config_base.php';
    $configured=0;
}
$post = filter_input_array(INPUT_POST);
$get = filter_input_array(INPUT_GET);
if ($configured) {
    if (isset($get['lib_id']) && $get['lib_id'] >= 0) {
        if (isset($plex_libs[$get['lib_id']])) {
            $lib_id=$get['lib_id'];
        } else {
            $lib_id=0;
        }    
    } elseif (isset($_COOKIE['lib_id']) && $_COOKIE['lib_id'] >= 0 && isset($plex_libs[$_COOKIE['lib_id']])) {
        $lib_id=$_COOKIE['lib_id'];
    } else {
        $lib_id=0;
    }
    setcookie('lib_id', $lib_id);
}

require "lang/$lang.php";
require 'log.php';
require 'template.php';
require 'classes/folder.php';
require 'classes/episode.php';
require 'classes/season.php';
require 'classes/title.php';
require 'classes/library.php';
require 'functions.php';

//Check configuration
if (!$configured) {
    echo $strings['unconfigured'];
    exit();
}
if (!isset($media_lang)) { //use interface language if media language not set
    $media_lang=$lang;
}

//Initialization
$log = new Log();
$tpl = new Template();
$data = array();
$root_media = new Folder(SRC_FOLDER);
$plex_lib= $plex_libs[$lib_id];
$library = new Library($plex_lib['path'],$plex_lib['name'],$plex_lib['type']);
$library->loadLibrary();
$ajax=0;

//Data (main template array)
isset($auth_user) ? $data['auth_user'] = $auth_user : $data['auth_user']="Anonymous";
$data['strings']=$strings;
$data['hostname'] = HOSTNAME;
$data['src_root_path'] = SRC_FOLDER;
$data['plex_root']['path'] = $plex_lib['path'];
$data['plex_root']['name'] =  $plex_lib['name'];
$data['plex_root']['type'] =  ($plex_lib['type'] == "shows" || $plex_lib['type'] == "movies" ? $plex_lib['type'] : "shows");
$data['plex_root']['id'] = $lib_id;
$data['plex_libs'] = $plex_libs;
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
             foreach ($title_data['episodes'] as $key=>$episode_data) {
                 if (in_array($key,$title_data['use_episodes'])) {
                     $episode=new Episode(s_quotes($episode_data['path']));
                     $episode->setName($episode_data['id']);
                     if(isset($episode_data['sub']) && !empty($episode_data['sub'])) {
                         foreach ($episode_data['sub'] as $sub) {
                             $episode->addSub(s_quotes($sub['path']), s_quotes($sub['name']));
                         }
                         $episode->setPriorSub(s_quotes($title_data['pref_folder_sub']));
                     }  
                     if(isset($episode_data['aud']) && !empty($episode_data['aud'])) {
                         foreach ($episode_data['aud'] as $aud) {
                             $episode->addAudio(s_quotes($aud['path']), s_quotes($aud['name']));
                         }
                         $episode->setPriorAudio(s_quotes($title_data['pref_folder_aud']));
                     }
                     $title->addEpisode($title_data['season'], $episode, $episode_data['id']);
                 }   
             }
            //Adding title to library
                if ($library->addTitle($title)) {
                    //And writinh
                    if($library->Save($title_name)) {
                        if ($title_data['season'] == 0) {
                            $msg=$strings['msg_specials'];
                        } else {
                            $msg=$title_data['season'].$strings['msg_season'];
                        }
                        if ($library->getType() == "shows") {
                            $json['status']=sprintf($strings['title_add_success'],$title_name,$msg,$plex_lib['name']);
                        }
                        if ($library->getType() == "movies") {
                            $json['status']=sprintf($strings['movie_add_success'],$title_name,$plex_lib['name']); 
                        }
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
      $data['src_folder_media']=  s_quotes($post['src_folder_media']);  
      $title_data=parseAnime(s_quotes($post['src_folder_media']),isset($post['src_folder_sub']) ? s_quotes($post['src_folder_sub']) : "",isset($post['src_folder_audio']) ? s_quotes($post['src_folder_audio']) : "");
      if ($title_data) {
          $title=array();
          //Output parsing data into form
          $title['name']=$title_data->getName();
          $season_num=$title_data->topSeason();
          $title['sub_folders']=$title_data->getSub_folders($season_num);
          $title['aud_folders']=$title_data->getAud_folders($season_num);
          $episodes=$title_data->getEpisodes($season_num);
          $title['season_num']=$season_num;
          $title['episodes']=array();
          $used_id=array();
          $guessed=array(); //Array for episodes with recognized number
          $not_guessed=array(); //Array for episodes with unrecognized number
          $k=0;
          $j=0;
          $max_id=0;
          foreach ($episodes as $episode) { //Splitting episode data by these two arrays for later sorting
                  if (isset($post['guess_ep_numbers'])) {
                     $id=guessEpisodeNumber($episode->getName(),$title['name']); 
                  } else {
                      $id=0;
                  }
                 if ($id && !in_array($id, $used_id)) {
                    $guessed[$j]['name']=$episode->getName();
                    $guessed[$j]['path']=$episode->getPath();
                    $guessed[$j]['sub']=$episode->getSubs();
                    $guessed[$j]['aud']=$episode->getAud(); 
                    $guessed[$j]['id']=$id;
                    $used_id[]=$id;
                    $max_id=($id > $max_id ? $id : $max_id);
                    $j++;
                 } else {
                    $not_guessed[$k]['name']=$episode->getName();
                    $not_guessed[$k]['path']=$episode->getPath();
                    $not_guessed[$k]['sub']=$episode->getSubs();
                    $not_guessed[$k]['aud']=$episode->getAud(); 
                    $not_guessed[$k]['id']=$k+1; 
                    $k++;
                 }
          }
          usort($guessed,"episodes_usort");
          for ($i=0;$i<count($not_guessed);$i++) { //Shifting unrecognized and duplicate episodes to the end
              $not_guessed[$i]['id']=$not_guessed[$i]['id']+$max_id;
          }
          $title['episodes']=array_merge($guessed, $not_guessed);
          //Sorting finished
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
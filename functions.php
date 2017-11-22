<?php
/*
 * Various functions available directly from main script
 * 
 */
//Recursive subtitle and audio adding to the episode
function addMediaRecursive($sub_folders,$media_folder,$title,$season,$media_type="sub") { 
  if (!empty($sub_folders)) {
        foreach ($sub_folders as $path) {
            //Checking if folder has subfolders
            $folder = new Folder($path);
            $subfolders = $folder->getSubfolders();
            if ($subfolders) {
                $minor=array();
                foreach ($subfolders as $key=>$value) {
                    $minor[]=$path."/".$value;
                }
                addMediaRecursive($minor, $media_folder, $title, $season, $media_type);  
            }
            //
            if ($path == $media_folder) {
                $parts=explode("/",$path);
                $subfoldname=array_pop($parts);
            } else {
                $subfoldname = str_replace($media_folder."/","",$path);
            }    
            $subfoldname=str_replace(SRC_FOLDER."/","",$subfoldname);
            $files=$folder->getFiles();
            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file['type'] == $media_type) { 
                        $match_id=$title->getEpisodeIdByName($season,$file['title']);
                        if ($match_id >=0) {
                              $title->episodeAddMedia($season,$match_id, $file['dir']."/".$file['name'],$subfoldname, $media_type); 
                        }
                    }
                }
            }
        }
    } 
    return $title;
}
//Stripping title name
function parseName($name) {
    $name=$name;
    //Place for regular expressions
    $name=preg_replace('/\[[^\]]*\]/',"",$name); //removing all in []
    //
    $name=trim($name);

    return $name;
}
//Guessing episode number (when changing - test on following)
function guessEpisodeNumber($name) {
    $num=0;
    //Regular expr
    $name=preg_replace('/\[[^\]]*\]/',"",$name); //removing all in []
    $name=preg_replace('/\([^\)]*\)/',"",$name); //removing all in ()
    $name=preg_replace('/s?[0-9]{0,3}ep?/i'," ",$name); //removing s01e, EP and such things before number
    if(preg_match_all('/(^| )[0-9]{1,3}/',$name,$matches,PREG_SET_ORDER)) {
        $num=intval(trim($matches[count($matches)-1][0]));
    }
    return $num;
}

/*
 * Place for parser-checking names
[Yousei-raws] Sakurasou no Pet na Kanojo 01 [BDrip 1920x1080 x264 FLAC]
[Leopard-Raws] Bakuman. - 01 RAW (NHKE 1280x720 x264 AAC)
Steins;Gate EP01 [BDRip 1080p x264-Hi10P FLAC]
 */

//Main title parsing function
function parseAnime($media_folder,$sub_folders=array(),$audio_folders=array()) {
    $media=new Folder($media_folder);
    //Creating title
    $files=$media->getFiles();
    if (!empty($files)) {
        $title = new Title();
        $title->setName(parseName($media->getName()));
        $title->createSeason();
        //Filling title with episodes
        foreach ($files as $file) {
            if ($file['type'] == 'vid') { 
                $episode = new Episode($file['dir']."/".$file['name'],$file['title']);
                $title->addEpisode(1,$episode);
            }
        }
        if (!$title->episodesCount(1)) {
            return false;
        }
        //Adding subtitles to episodes
          $title=addMediaRecursive($sub_folders, $media_folder, $title, 1, "sub");
        //Adding audio to episodes
          $title=addMediaRecursive($audio_folders, $media_folder, $title, 1, "aud");
        //
   } else {
       return false;
   }
   return $title;
}

//Checking title form data
function validateTitleData($title_data,$library) {
        $json=array();
        global $strings; //Loading language
        $title_data['name']=trim($title_data['name']);
        if (strlen($title_data['name']) < 1 || strlen($title_data['name']) > 50) {
            $json['error'] = $strings['err_title_name_length'];
        } elseif (preg_match('/[\/<>&\'"]+/',$title_data['name'])) {
            $json['error'] = $strings['err_title_name_symbol'];
        } elseif (!isset($title_data['season'])) {
            $json['error'] = $strings['err_select_season'];
        } elseif (!isset($title_data['use_episodes']) || empty($title_data['use_episodes'])) {
            $json['error'] = $strings['err_select_episode'];
        } elseif ($library->hasTitle($title_data['name'])) {
                $json['warning'] = sprintf($strings['title_exists'],$title_data['season'],$title_data['name']);
                if ($library->titleHasSeason($title_data['name'], $title_data['season'])) {
                  $json['warning'] = sprintf($strings['season_exists'],$title_data['season'],$title_data['name']);
                }
        }
        return $json;
}
?>
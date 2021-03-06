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
                    $subfoldname_extra=$subfoldname;
                    if ($file['type'] == $media_type) { 
                        if (strpos($file['title'],".")) {
                            $parts=explode(".",$file['title']); //recognition of external media named like title.[studio].somecrap.ass
                            $titlepart=$parts[0];
                            $i=1;
                            while ($i<=count($parts)) {
                                $match_id=$title->getEpisodeIdByName($season,$titlepart);
                                if ($match_id >=0) {
                                    if ($i<count($parts)) $subfoldname_extra=$subfoldname_extra." ".str_replace($titlepart.".", "", $file['title']);
                                    break;
                                }
                                if ($i<count($parts)) $titlepart.=".".$parts[$i];
                                $i++;
                            }
                        } else {
                            $match_id=$title->getEpisodeIdByName($season,$file['title']);
                        }    
                        if ($match_id >=0) {
                              $title->episodeAddMedia($season,$match_id, $file['dir']."/".$file['name'],$subfoldname_extra, $media_type); 
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
    //Film tags processing
    $name=preg_replace('/\.\(?([0-9]{4})\)?\./',".(\\1).",$name); //parse year
    $name=preg_replace('/(\S)\.(\S)/',"\\1 \\2",$name); //parse dotted.style.naming
    $name=preg_replace('/(72|108)0p/',"",$name);
    $name=preg_replace('/(\S*264|\d?x?rus|\d?x?eng|\d?x?ukr|\d?x?mvo|Extended|blu.?ray|DTS|WEB-DL|\S*AC3|Open matte|HDTV|BD.?Rip|dvd.?rip|hdtv.?rip)/Ui',"",$name);
    
    //Anime tag processing
    $name=preg_replace('/\[[^\]]*\]/',"",$name); //removing all in []
    $name=preg_replace('/(TV|season ?)[0-9]{1,2}/Ui',"",$name); //removing season numeration
    //$name=preg_replace('/\([0-9]{4}\)/',"",$name); //removing year *disabled for films*
    
    //
    $name=trim($name);

    return $name;
}
function guessSeason($name) { //guessing season number
    $num=1;
    //Place for regular expressions
    if (preg_match('/(TV|season ?)[0-9]{1,2}/Ui',$name,$matches)) {
       $res=intval(preg_replace("/[^0-9]*/","",$matches[0]));
       if ($res < 10 && $res > 1) $num=$res;
    }
    //
    return $num;
}
//Guessing episode number (when changing - test on following)
function guessEpisodeNumber($name,$title_name="") {
    $num=0;
    //Regular expr
    $name=str_replace($title_name,"",$name); //removing title name
    $name=preg_replace('/\[([0-9][0-9])\]/i',' ${1} ',$name); // getting [01] style numeration 
    $name=preg_replace('/\(([0-9][0-9])\)/i',' ${1} ',$name); // getting (01) style numeration
    $name=preg_replace('/\[[^\]]*\]/',"",$name); //removing all in []
    $name=preg_replace('/\([^\)]*\)/',"",$name); //removing all in ()
    $name=preg_replace('/\'[^\']*\'/',"",$name); //removing all in 'quotes'
    $name=preg_replace('/"[^\']*"/',"",$name); //removing all in "quotes"
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
[OZC] Ghost in the Shell Ep 01 'Section 9' [Blu-Ray 720p]
[OZC] Ghost in the Shell Ep 08 "Section 19" [Blu-Ray 720p]
[Leopard-Raws] Bakuman 2 - 01 RAW (NHKE 1280x720 x264 AAC)
[Leopard-Raws] Bakuman 3.0 - 01 RAW (NHKE 1280x720 x264 AAC)
My.Neighbour.Totoro.720p.mkv
 */

//Main title parsing function
function parseAnime($media_folder,$sub_folders=array(),$audio_folders=array()) {
    $media=new Folder($media_folder);
    //Creating title
    $files=$media->getFiles();
    if (!empty($files)) {
        $title = new Title();
        $title->setName(parseName($media->getName()));
        $season_num=guessSeason($media->getName());
        $title->createSeason($season_num);
        //Filling title with episodes
        foreach ($files as $file) {
            if ($file['type'] == 'vid') { 
                $episode = new Episode($file['dir']."/".$file['name'],$file['title']);
                $title->addEpisode($season_num,$episode);
            }
        }
        if (!$title->episodesCount($season_num)) {
            return false;
        }
        //Adding subtitles to episodes
          $title=addMediaRecursive($sub_folders, $media_folder, $title, $season_num, "sub");
        //Adding audio to episodes
          $title=addMediaRecursive($audio_folders, $media_folder, $title, $season_num, "aud");
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
        if (strlen($title_data['name']) < 1 || strlen($title_data['name']) > 100) {
            $json['error'] = $strings['err_title_name_length'];
        } elseif (preg_match('/[\/<>\&]+/',$title_data['name'])) {
            $json['error'] = $strings['err_title_name_symbol'];
        } elseif (!isset($title_data['season'])) {
            $json['error'] = $strings['err_select_season'];
        } elseif (!isset($title_data['use_episodes']) || empty($title_data['use_episodes'])) {
            $json['error'] = $strings['err_select_episode'];
        } elseif ($library->hasTitle($title_data['name'])) {
                if ($title_data['season'] == 0) {
                    $msg=$strings['msg_specials'];
                } else {
                    $msg=$title_data['season'].$strings['msg_season'];
                }
                if ($library->getType() == "shows") {
                    $json['warning'] = sprintf($strings['title_exists'],$library->name,$msg,$title_data['name']);
                    if ($library->titleHasSeason($title_data['name'], $title_data['season'])) {
                      $json['warning'] = sprintf($strings['season_exists'],$msg,$title_data['name'],$library->name);
                    }
                }
                if ($library->getType() == "movies") {
                    $json['warning'] = sprintf($strings['movie_exists'],$title_data['name'],$library->name);
                }
        }
        return $json;
}

function episodes_usort($a,$b) { //Sorting episodes array by guessed id before printing into template
    if ($a['id'] == $b['id']) {
        return 0;
    }
    return ($a['id'] < $b['id']) ? -1 : 1;
}

function e_quotes($str) { //escape quotes for html
    return str_replace("'","&sq;",str_replace('"',"&dq;",$str));
}
function s_quotes($str) { //strip escaped quotes for html
    return str_replace("&sq;","'",str_replace("&dq;",'"',$str));
}
?>
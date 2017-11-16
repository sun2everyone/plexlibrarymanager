<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Рекурсивное добавление субтитров и озвучки
function addMediaRecursive($sub_folders,$media_folder,$title,$season,$media_type="sub") { 
  if (!empty($sub_folders)) {
        foreach ($sub_folders as $path) {
            //Проверяем на подпапки
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
            $subfoldname = str_replace($media_folder."/","",$path);
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
//Парсинг имени с убиранием лишнего
function parseName($name) {
    $name=$name;
    //Регулярные выражения
    $name=preg_replace('/\[\S*\]/',"",$name);
    $name=trim($name);
    //
    return $name;
}
//Угадывание номера эпизода
function guessEpisodeNumber($name) {
    $num=0;
    $name=preg_replace('/s?[0-9]{1,2}e/'," ",$name);
    if(preg_match('/(^| )[0-9]{1,2} ?/',$name,$matches)) {
        $num=intval(trim($matches[0]));
    }
    return $num;
}
//Главная функция парсинга тайтла
function parseAnime($media_folder,$sub_folders=array(),$audio_folders=array()) {
    $media=new Folder($media_folder);
    //Создаем тайтл
    $files=$media->getFiles();
    if (!empty($files)) {
        $title = new Title();
        $title->setName(parseName($media->getName()));
        $title->createSeason();
        //Наполняем тайтл эпизодами
    foreach ($files as $file) {
        if ($file['type'] == 'vid') { 
            $episode = new Episode($file['dir']."/".$file['name'],$file['title']);
            $title->addEpisode(1,$episode);
        }
    }
    if (!$title->episodesCount(1)) {
        return false;
    }
    //Добавляем субтитры к тайтлу
      $title=addMediaRecursive($sub_folders, $media_folder, $title, 1, "sub");
    //Добавляем озвучку к эпизодам
      $title=addMediaRecursive($audio_folders, $media_folder, $title, 1, "aud");
    //
   } else {
       return false;
   }
   return $title;
}

//Проверка данных формы тайтла
function validateTitleData($title_data,$library) {
        $json=array();
        $title_data['name']=trim($title_data['name']);
        if (strlen($title_data['name']) < 1 || strlen($title_data['name']) > 255) {
            $json['error'] = 'Неправильное имя тайтла! Имя должно содержать от 1 до 255 символов!';
        } elseif (preg_match('/[\/<>&\'"]+/',$title_data['name'])) {
            $json['error'] = 'Неправильное имя тайтла! Введен недопустимый символ!';
        } elseif (!isset($title_data['season'])) {
            $json['error'] = 'Нужно выбрать сезон!';
        } elseif ($library->hasTitle($title_data['name'])) {
                $json['warning'] = 'Тайтл с таким названием уже есть в библиотеке! При продолжении папка будет добавлена как '.$title_data['season'].' сезон '.$title_data['name'].'.';
                if ($library->titleHasSeason($title_data['name'], $title_data['season'])) {
                  $json['warning'] = $title_data['season']." сезон ".$title_data['name']." уже есть в библиотеке. При продолжении он будет перезаписан!";
                }
        }
        return $json;
}
?>
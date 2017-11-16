<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.

const MAXDEPTH = 10;

function calcSymlinkPath($target_path,$link_dir) { //Путь для относительного симлинка
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
   
  $target_path="folder/Proxy.mkv";
  $link="/Anime";
  echo $target_path."<br>";
  echo $link."<br>";
  echo calcSymlinkPath($target_path, $link);
 *  */

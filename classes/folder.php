<?php

/**
 * Description of folder
 *
 * @author sun2
 */
class Folder {
    private $path;
    private $name;
    private $subfolders = Array ();
    private $files = Array();
    
    public function __construct($path) {
        if (!is_dir($path) || !is_readable($path)) {
            exit("Directory $path unavailable! Incrorrect path!");
        }
        try {
          $dir = opendir($path);
          $this->path=$path;
          $path_info=pathinfo($path);
          $this->name=$path_info['basename'];
          while ($file = readdir ($dir)) 
            {
              if (($file != ".") && ($file != "..")) {
                if(is_dir($path."/".$file)) {
                    $this->subfolders[]=$file;
                } elseif (is_file($path."/".$file)) {
                    $path_info=pathinfo($path."/".$file);
                    $this->files[$file]['dir']=$path_info['dirname'];
                    if (isset($path_info['extension'])) {
                        $this->files[$file]['ext']=$path_info['extension'];
                    } else {
                         $this->files[$file]['ext']="";
                    }
                    $this->files[$file]['type']=$this->fileGetType($this->files[$file]['ext']);
                    $this->files[$file]['name']=$path_info['basename'];
                    $this->files[$file]['title']=$path_info['filename'];
                }
              }  
              array_multisort($this->files);
              array_multisort($this->subfolders);
            }
            closedir($dir);  
        } catch (Exception $e) {
            echo "Can not read directory $path! Exception caught.";
        }    
	}
    public function getName() {
        return $this->name;
    }
        
    public function getFiles() {
       if (!empty($this->files)) {
           return $this->files;
       } else return false;
    } 
    public function getVideos() {
        $files=array();
        foreach ($this->files as $file) {
            if ($this->isVideo($file['ext'])) {
                $files[]=$file;
            }
        }
        return $files;
    }
    public function getSubs() {
        $files=array();
        foreach ($this->files as $file) {
            if ($this->isSubtitle($file['ext'])) {
                $files[]=$file;
            }
        }
        return $files;
    }
    public function getAudios() {
        $files=array();
        foreach ($this->files as $file) {
            if ($this->isAudio($file['ext'])) {
                $files[]=$file;
            }
        }
        return $files;
    }
    public function getFolder() {
        $folder = Array();
        $folder['files']=$this->files;
        $folder['path']=$this->path;
        $folder['subfolders']=$this->subfolders;
        return $folder;
    }
    public function getSubfolders() {
        $empty = array(); 
        if (!empty($this->subfolders)) {
           return $this->subfolders;
       } else return $empty;
    }
    //Guessing filetype by extension
    private function isVideo($ext) {
        $extensions = array(
            "avi",
            "mkv",
            "mp4",
            "mpeg"
        );
        if (in_array($ext, $extensions)) return true; else return false;
    }
    private function isAudio($ext) {
        $extensions = array(
            "ac3",
            "mp3"
        );
        if (in_array($ext, $extensions)) return true; else return false;
    }
    private function isSubtitle($ext) {
        $extensions = array(
            "ass",
            "srt"
        );
        if (in_array($ext, $extensions)) return true; else return false;
    }
    private function fileGetType($ext) {
        if ($this->isVideo($ext)) return "vid";
        elseif ($this->isSubtitle($ext)) return "sub";
        elseif ($this->isAudio($ext)) return "aud";
        else return "file";
    }

}

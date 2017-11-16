<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of title
 *
 * @author sun2
 */
class Season {
    //put your code here
    private $number;
    private $episodes = array();
    private $sub_folders = array();
    private $aud_folders = array();
    
    public function __construct($number = -1) {
        if ($number > 0) {
            $this->number=intval($number);
        } else {
             $this->number=1;
        }
    }
    public function addEpisode($episode,$id=0) {
        if (!intval($id)) {
            $this->episodes[]=$episode;
        } else {
            $this->episodes[$id]=$episode;
        }    
    }
    public function setNumber($number) {
        $this->number=intval($number);
    }
    public function getNumber() {
        return $this->number;
    }
    public function getSub_folders() {
        return $this->sub_folders;
    }
    public function getAud_folders() {
        return $this->aud_folders;
    }
    public function getEpisodes() {
            return $this->episodes;
    }
    public function episodesCount() {
        return count($this->episodes);
    }
    public function episodeAddMedia($id,$path,$dir="",$type="") {
        if ($type=="aud") {
            $this->episodes[$id]->addAudio($path,$dir);
            if (!in_array($dir, $this->aud_folders)) { $this->aud_folders[]=$dir; }
        } elseif ($type=="sub") {
            $this->episodes[$id]->addSub($path,$dir); 
            if (!in_array($dir, $this->sub_folders)) { $this->sub_folders[]=$dir; }
        }
    }
    public function getEpisodeIdByName($name) {
        $ep_id=-1;
        foreach ($this->episodes as $id=>$episode) {
            if ($episode->getName() == $name) {
                $ep_id=$id;
                break;
            }
        }
        return $ep_id;
    }
    
}
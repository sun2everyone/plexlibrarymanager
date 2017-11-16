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
class Title {
    //put your code here
    /*
    private $name;
    private $episodes = array();
    private $sub_folders = array();
    private $aud_folders = array();
    
    public function addEpisode($episode) {
        $this->episodes[]=$episode;
    }
    public function setName($name) {
        $this->name=$name;
    }
    public function getName() {
        return $this->name;
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
    public function getEpisodeByName($name) {
        $ep_id=-1;
        foreach ($this->episodes as $id=>$episode) {
            if ($episode->getName() == $name) {
                $ep_id=$id;
                break;
            }
        }
        return $ep_id;
    } */
    private $name;
    private $seasons = array();
    
    public function createSeason($number = 0) {
        $number = intval($number);
        if ($number > 0) {
         $this->seasons[$number]= new Season($number);
        } else {
            $number = count($this->seasons)+1; 
            $this->seasons[$number]= new Season($number);
        }
    }
    public function seasonsCount() {
        return count($this->seasons);
    }
    public function removeSeason($number) {
        if (intval($number) >=0) {
             foreach ($this->seasons as $id=>$season) {
                 if($id == $number) {
                     unset($this->seasons[$id]);
                 }
             }
            return true;
        } else {
            return false;
        }
    }
    public function setName($name) {
        $this->name=trim(str_replace("/", "_", $name)); //Protect from / in paths
    }
    public function getName() {
        return $this->name;
    }
    public function getSeasons() {
        return $this->seasons;
    }
    public function addSeason($season) {
        $this->seasons[$season->getNumber()]=$season;
    }
    public function getSeason($number) {
        $season = array();
        if (isset($this->seasons[$number])) {
            $season=$this->seasons[$number];
        } 
            return $season;
    }
    public function hasSeason($number) {
       if (isset($this->seasons[intval($number)])) return true; else return false; 
    }
    
    public function getSub_folders($season) {
        $folders=array();
        if (isset($this->seasons[$season])) {
            $folders=$this->seasons[$season]->getSub_folders();
        }
        return $folders;
    }
    public function getAud_folders($season) {
        $folders=array();
        if (isset($this->seasons[$season])) {
            $folders=$this->seasons[$season]->getAud_folders();
        }
        return $folders;
    }
     public function episodeAddMedia($season,$id,$path,$dir="",$type="") {
         if (isset($this->seasons[$season])) {
          $this->seasons[$season]->episodeAddMedia($id,$path,$dir,$type);
         }
     }
     public function getEpisodes($season) {
            $episodes = array();
            if (isset($this->seasons[$season])) {
            $episodes = $this->seasons[$season]->getEpisodes();
            } 
            return $episodes;
    }
    public function episodesCount($season) {
            if (isset($this->seasons[$season])) {
                $episodes = $this->seasons[$season]->getEpisodes();
                return count($episodes);
            } else {
                return 0;
            }
        
    }
    public function addEpisode($season,$episode,$id=0) {
         if (isset($this->seasons[$season])) {
            $this->seasons[$season]->addEpisode($episode,$id);
         }
    }
    public function getEpisodeIdByName($season,$name) {
        $ep_id=-1;
        if (isset($this->seasons[$season])) {
            $ep_id=$this->seasons[$season]->getEpisodeIdByName($name);
         } 
       return $ep_id;  
    }
}

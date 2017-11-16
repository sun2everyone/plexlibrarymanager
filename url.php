<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of url
 *
 * @author sun2
 */
class url {
    private $root;
    //put your code here
    public function __construct() {
        $this->root=HOSTNAME;
    }
    public function url($paths) {
        $url="";
        
        if(!empty($paths)) {
            if (is_array($paths)) {
                $url.="index.php?";
                for ($i=0;$i<count($paths);$i++) {
                    $url.=$paths[$i];
                    if (($i+1) < count($paths)) { $url.="&"; }
                }
            } else {
                $url="index.php?".$paths;
            }
        }
        return $this->root.$url;
        
    }
}
?>

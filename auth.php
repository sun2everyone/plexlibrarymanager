<?php

function authenticate() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        Header("WWW-Authenticate: Basic realm=\"Restricted\"");
        Header("HTTP/1.0 401 Unauthorized");
        exit();
    } 
    }
?>


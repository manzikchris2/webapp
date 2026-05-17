<?php

function login(){
     if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_COOKIE['token'])) {
        $token = $_COOKIE['token'];
        
        // Check if session exists and user is logged in
        if (!isset($_SESSION[$token]) || !isset($_SESSION[$token]['loggedin']) || $_SESSION[$token]['loggedin'] !== true) {
            $location = isset($_SESSION[$token]['on']) ? $_SESSION[$token]['on'] . ".html" : "home.html";
            header("Location: " . $location);
            exit();
        }
    
    } else {
        
        header("Location: home.html");
        exit();
    }

   
}

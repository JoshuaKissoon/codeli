<?php

    /*
     * This is the website index page that handles all requests throughout the site
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);

    /**
     * Lets bootstrap the system
     */
    require_once 'system/bootstrap.inc.php';


    /**
     * Render the basic theme
     */
    Codeli::getInstance()->getThemeRegistry()->renderPage();
    exit;
    
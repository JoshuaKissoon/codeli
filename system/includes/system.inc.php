<?php

    /**
     * This file is the controller for the entire system. 
     * It loads and runs all the necessary system objects and handles core system functionalities.
     * 
     * @author Joshua Kissoon
     * @since 20140616
     */
    $url = Codeli::getInstance()->getURL();


    /* If we're at admin section! load the admin template */
    if (isset($url[0]) && $url[0] == SiteConfig::adminUrlDirectory())
    {
        SiteConfig::$useAdminTheme = true;
    }
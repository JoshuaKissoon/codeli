<?php

    /**
     * This file is the controller for the entire site. 
     * It loads and runs all the necessary site objects and handles core site functionalities.
     * 
     * @author Joshua Kissoon
     * @since 20140616
     */
    /*
     * Loading API Specific functions
     */
    require_once SiteConfig::modulesPath() . 'api/functions.php';

    /*
     * Generating code 
     * @desc Indrajeet Specific functionalify
     */
    if (isset($_REQUEST['generate']))
    {
        $index = 'ng-app-old/test.php';
        require_once SystemConfig::basePath() . $index;
        exit();
    }

    /*
     * Including Angular App and script resources related to it
     */
    if (!isset($_REQUEST['urlq']))
    {
        $index = 'index.php';
        require_once SiteConfig::angularAppDirectory() . $index;
    }
    else
    {
        $sm = ServiceManager::getInstance();
        if (!$sm->checkApiEndpoint())
        {
            $sm->publish();
            exit();
        }
        if (!$sm->checkApiPermission())
        {
            $sm->publish();
            exit();
        }

        $sm->callApiFunction();
        $sm->publish();
    }
    
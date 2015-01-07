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


    $url = Codeli::getInstance()->getURL();
    
    if (BaseConfig::HOME_URL == trim($url[0]))
    {
        /**
         * Render the basic theme if it's not an api call
         * 
         * This will work for all angular URLs since AngularJS URLs have no urlq variable set
         * 
         * With no urlq variable set, the $url will be set to /home
         */
        Codeli::getInstance()->getThemeRegistry()->renderPage();
    }
    else
    {
        /**
         * @section Load the modules for this url 
         */
        $handlers = JPath::getRoutes();
        foreach ($handlers as $handler)
        {
            if (null == $handler->getPermissionId() || "" == $handler->getPermissionId())
            {
                /* There is no permission for this module at the current URL, just load it */
                include_once JModuleManager::getModule($handler->getModule());
            }
            //else if ($codeli->getUser()->hasPermission($handler->getPermissionId()))
            //{
            /* If the user has the permission to access this module for this URL, load the module */
            include_once JModuleManager::getModule($handler->getModule());
            //}

            $response = call_user_func($handler->getCallback());
            print $response->getJSONOutput();
        }
    }
    exit;
    
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
        try
        {
            $handler = JPath::getRoute();
        }
        catch (InvalidRouteException $ex)
        {
            $response = new APIResponse("", "System Error Occured", false);
            print $response->getJSONOutput();
            exit;
        }

        /* Check if the user can access this path */
        $user = Codeli::getInstance()->getUser();
        $access = false;

        /* There is no permission for this module at the current URL, just load it */
        if (null == $handler->getPermissionId() || "" == $handler->getPermissionId() || "0" == $handler->getPermissionId())
        {
            $access = true;
        }
        else if ($user->hasPermission($handler->getPermissionId()))
        {
            $access = true;
        }

        if (false == $access)
        {
            $response = new APIResponse("", "No permission access.", false);
            print $response->getJSONOutput();
            exit;
        }

        /* User has full access, lets load all active modules */
        $modules = JModuleManager::getActiveModules();
        foreach ($modules as $module)
        {
            include_once JModuleManager::getModule($module->getId());
        }

        $response = call_user_func($handler->getCallback());
        print $response->getJSONOutput();
        exit;
    }
    
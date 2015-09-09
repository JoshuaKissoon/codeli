<?php

    /*
     * This is the website index page that handles all requests throughout the site
     * 
     * @author Joshua Kissoon
     * @since 2014
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);

    /* Bootstrap the system */
    require_once 'system/bootstrap.inc.php';

    function default_exception_handler(Exception $e)
    {
        // show something to the user letting them know we fell down
        echo "<h2>Something Bad Happened</h2>";
        echo "<p>We fill find the person responsible and have them shot</p>";
        // do some logging for the exception and call the kill_programmer function.
    }

    set_exception_handler("default_exception_handler");

    $url = Codeli::getInstance()->getURL();

    /* Hanle invalid routes */
    try
    {
        $handler = JPath::getRouteHandler();
    }
    catch (InvalidRouteException $e)
    {
        print "got here";
        $response = new APIResponse("", "System Error Occured", false, APIResponse::STATUS_CODE_INVALID_URL);
        $response->output();
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

    
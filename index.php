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

    set_exception_handler("default_exception_handler");

    $url = Codeli::getInstance()->getURL();

    /* Hanle invalid routes */
    try
    {
        $handler = JPath::getRouteHandler();
    }
    catch (InvalidRouteException $e)
    {
        $response = new APIResponse("", "No URL Handler", false, APIResponse::STATUS_CODE_INVALID_URL);
        $response->output();
        exit;
    }

    /* Check if the user can access this path */
    $user = Codeli::getInstance()->getUser();
    $access = false;

    /* There is no permission for this module at the current URL, just load it */
    if (null == $handler->getPermissionId() || "" == $handler->getPermissionId() ||
            "0" == $handler->getPermissionId() || $user->hasPermission($handler->getPermissionId()))
    {
        $access = true;
    }

    if (false == $access)
    {
        $response = new APIResponse("", "No permission access.", false);
        $response->output();
        exit;
    }

    /* User has full access, lets load all active modules */
    $modules = JModuleManager::getActiveModules();
    foreach ($modules as $module)
    {
        include_once JModuleManager::getModule($module->getId());
    }

    $response = call_user_func($handler->getCallback());
    $response->output();
    exit;

    function default_exception_handler(Exception $e)
    {
        $response = new APIResponse("", "System Error Occured", false, APIResponse::STATUS_SYSTEM_ERROR);
        $response->output();
    }
    
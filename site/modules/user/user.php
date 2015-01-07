<?php

    $usermod_path = JModuleManager::getModulePath("user");
    $usermod_url = JModuleManager::getModuleURL("user");
    $usermod_rel_url = "admin/user/";

    $url = Codeli::getInstance()->getURL();

    function user_get_users()
    {
        $users = array();
        $users[] = "James";
        $users[] = "Peter";
        $users[] = "Paul";
        $users[] = "Joshua";
        
        return new APIResponse($users);
    }
    
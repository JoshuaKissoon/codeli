<?php

    require_once 'classes/RBAC.php';
    require_once 'rbac.php';

    /**
     * Function that adds a new user to te system
     */
    function user_add_user()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user = new JUser();
        $user->setUserId($data->userId);
        $user->setPassword($data->password);
        $user->setEmail($data->email);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setOtherName($data->otherName);

        if (!$user->insert())
        {
            $al = new ActivityLog(Codeli::getInstance()->getUser()->getId(), SystemLogger::OBJECT_USER, "", SystemLogger::ACTION_INSERT, "Failure", "", $user->expose());
            SystemLogger::log($al);
            return new APIResponse("", "User addition failed.", false);
        }

        /* Lets add user Roles */
        foreach ($data->roles as $rid => $has_role)
        {
            if ($has_role)
            {
                $ur = new UserRole();
                $ur->setRoleId($rid);
                $ur->setUserId($user->getId());
                $ur->insert();
            }
        }

        $al = new ActivityLog(Codeli::getInstance()->getUser()->getId(), SystemLogger::OBJECT_USER, $user->getId(), SystemLogger::ACTION_INSERT, "Success", "", $user->expose());
        SystemLogger::log($al);
        return new APIResponse($user->expose(), "Successfully added new user.", true);
    }

    /**
     * Edit a user account
     */
    function user_edit_user()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!JUser::isExistent($data->uid))
        {
            return new APIResponse("", "Invalid User Data", "");
        }

        $user = new JUser($data->uid);
        $original = $user->expose();
        $user->setUserId($data->userId);
        $user->setPassword($data->password);
        $user->setEmail($data->email);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setOtherName($data->otherName);

        if (!$user->update())
        {
            $al = new ActivityLog(SessionManager::loggedInUid(), SystemLogger::OBJECT_USER, $user->getId(), SystemLogger::ACTION_UPDATE, "Failure", $original, $user->expose());
            SystemLogger::log($al);
            return new APIResponse("", "User updation failed.", false);
        }

        /* Lets add user Roles */
        foreach ($data->roles as $rid => $has_role)
        {
            if ($has_role)
            {
                $ur = new UserRole();
                $ur->setRoleId($rid);
                $ur->setUserId($user->getId());
                $ur->insert();
            }
        }

        $al = new ActivityLog(SessionManager::loggedInUid(), SystemLogger::OBJECT_USER, $user->getId(), SystemLogger::ACTION_UPDATE, "Success", $original, $user->expose());
        SystemLogger::log($al);
        return new APIResponse($user->expose(), "Successfully updated user.", true);
    }

    /**
     * Retrieves all users from the database
     */
    function user_get_users()
    {
        $db = Codeli::getInstance()->getDB();

        $sql = "SELECT * FROM " . DatabaseTables::USER_V;
        $results = $db->query($sql);

        $users = array();
        while ($res = $db->fetchObject($results))
        {
            $user = new JUser();
            $user->loadFromMap($res);
            $users[] = $user->expose();
        }

        $al = new ActivityLog(SessionManager::loggedInUid(), SystemLogger::OBJECT_USER, "", SystemLogger::ACTION_VIEW, "Success");
        SystemLogger::log($al);

        return new APIResponse($users);
    }

    /**
     * Retrieves a single user
     */
    function user_get_user()
    {
        $url = Codeli::getInstance()->getURL();

        $uid = $url[2];

        if (!JUser::isExistent($uid))
        {
            return new APIResponse("", "Invalid User Data", false);
        }

        $user = new JUser($uid);

        $al = new ActivityLog(SessionManager::loggedInUid(), SystemLogger::OBJECT_USER, $user->getId(), SystemLogger::ACTION_VIEW, "Success");
        SystemLogger::log($al);

        return new APIResponse($user->expose());
    }

    /**
     * Logs in a user to the API
     * 
     * @todo Handle activity logging
     */
    function user_user_login()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user = new JUser();
        $user->setUserId($data->userId);
        $user->setPassword($data->password);

        $authenticated = $user->authenticate();

        if (!$authenticated)
        {
            return new APIResponse("", "Invalid user id and/or password", false);
        }

        /* Everything is good */
        $user->load();
        $token = SessionManager::loginUser($user->getId());
        $ret = array("token" => $token);
        return new APIResponse(json_encode($ret), "Successfully logged in.", true);
    }

    /**
     * Logs out a user from the API
     */
    function user_user_logout()
    {
        $success = SessionManager::logoutUser();

        return new APIResponse("", "User Logout. ", $success);
    }
    
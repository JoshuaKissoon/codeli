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

        foreach ($data->roles as $rid => $has_role)
        {
            if ($has_role)
            {
                $user->addRole($rid);
            }
        }

        if ($user->insert())
        {
            Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_INSERT, "Success", "", $user->expose());
            return new APIResponse($user->expose(), "Successfully added new user.", true);
        }
        else
        {
            Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_INSERT, "Failure", "", $user->expose());
            return new APIResponse("", "User addition failed.", false);
        }
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

        foreach ($data->roles as $rid => $has_role)
        {
            if ($has_role)
            {
                $user->addRole($rid);
            }
        }

        if ($user->update())
        {
            Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_UPDATE, "Success", $original, $user->expose());
            return new APIResponse($user->expose(), "Successfully updated user.", true);
        }
        else
        {
            Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_UPDATE, "Failure", $original, $user->expose());
            return new APIResponse("", "User updation failed.", false);
        }
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
            $user->loadRoles();
            $users[] = $user->expose();
        }

        Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_VIEW, "Success");

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
        $user->loadRoles();

        Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_VIEW, "Success");

        return new APIResponse($user->expose());
    }

    /**
     * Logs in a user to the API
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
            Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGIN, "Failed");
            return new APIResponse("", "Invalid user id and/or password", false);
        }

        /* Everything is good */
        $user->load();
        SessionManager::loginUser($user);

        Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGIN, "Success");

        return new APIResponse("", "Successfully logged in.", true);
    }

    /**
     * Logs out a user from the API
     */
    function user_user_logout()
    {
        Logger::log(SessionManager::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGOUT, "Success");

        $success = SessionManager::logoutUser();

        $response = new APIResponse();
        $response->setSuccess($success);

        return $response;
    }
    
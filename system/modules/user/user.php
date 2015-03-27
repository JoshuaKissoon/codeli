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
            Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_INSERT, "Success", "", $user->expose());
            return new APIResponse($user->expose(), "Successfully added new user.", true);
        }
        else
        {
            Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_INSERT, "Failure", "", $user->expose());
            return new APIResponse("", "User addition failed.", false);
        }
    }

    /**
     * Function that adds a new user to te system
     */
    function user_edit_user()
    {
        $data = json_decode(file_get_contents("php://input"));

        if(!JUser::isExistent($data->uid))
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
            Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_UPDATE, "Success", $original, $user->expose());
            return new APIResponse($user->expose(), "Successfully updated user.", true);
        }
        else
        {
            Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_UPDATE, "Failure", $original, $user->expose());
            return new APIResponse("", "User updation failed.", false);
        }
    }

    /**
     * Function that retrieves all users from the database
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

        Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_VIEW, "Success");

        return new APIResponse($users);
    }

    /**
     * Function that retrieves all users from the database
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

        Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_VIEW, "Success");

        return new APIResponse($user->expose());
    }

    function user_get_session()
    {
        $response = "";
        if (Session::isLoggedIn())
        {
            $response = new APIResponse(array("logged_in" => true, "user" => Codeli::getInstance()->getUser()->expose()));
        }
        else
        {
            $response = new APIResponse(array("logged_in" => false));
        }

        return $response;
    }

    function user_login_user()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user = new JUser();
        $user->setUserId($data->userId);
        $user->setPassword($data->password);

        $authenticated = $user->authenticate();

        if (!$authenticated)
        {
            Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGIN, "Failed");
            return new APIResponse("", "Invalid user id and/or password", false);
        }

        /* Everything is good */
        $user->load();
        Session::loginUser($user);

        Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGIN, "Success");

        return new APIResponse("", "Successfully logged in.", true);
    }

    function user_logout_user()
    {
        Logger::log(Session::loggedInUid(), Logger::OBJECT_USER, Logger::ACTION_LOGOUT, "Success");

        $success = Session::logoutUser();

        $response = new APIResponse();
        $response->setSuccess($success);

        return $response;
    }
    
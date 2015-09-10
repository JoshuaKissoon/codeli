<?php

    /**
     * File that contains all user RBAC controllers
     * 
     * @author Joshua Kissoon
     * @since 20150113
     */
    function rbac_role_add()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || "" == $data->title)
        {
            return new APIResponse("", "Please enter a title", false);
        }

        $obj = new Role();
        $obj->setTitle($data->title);
        $obj->setDescription($data->description);
        if ($obj->insert())
        {
            SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_INSERT, "Success", "", $obj->expose());
            return new APIResponse($obj->expose(), "Successfully added a new Role.", true);
        }
        else
        {
            SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_INSERT, "Failure", "", $obj->expose());
            return new APIResponse("", "Role addition failed.", false);
        }
    }

    function rbac_role_view()
    {
        $url = Codeli::getInstance()->getURL();

        if (!Role::isExistent($url[2]))
        {
            return new APIResponse("", "Invalid Role Id", false);
        }

        /* Seems to be valid data, lets get the role */
        $role = new Role($url[2]);

        SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_VIEW, "View Role with id " + $role->getId());

        return new APIResponse($role->expose());
    }

    function rbac_role_view_all()
    {
        $res = RBAC::getAllRoles();

        $return = array();

        foreach ($res as $ct)
        {
            $return[] = $ct->expose();
        }

        SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_VIEW, "View all Roles");

        return new APIResponse($return);
    }

    function rbac_role_edit()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || "" == $data->title)
        {
            return new APIResponse("", "Please enter a title", false);
        }

        if (!Role::isExistent($data->rid))
        {
            return new APIResponse("", "Invalid Role Id", false);
        }

        $role = new Role($data->rid);
        $original_object = $role->expose();
        $role->setTitle($data->title);
        $role->setDescription($data->description);
        if ($role->update())
        {
            SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_UPDATE, "Success", $original_object, $role->expose());
            return new APIResponse($role->expose(), "Successfully updated the Role.", true);
        }
        else
        {
            SystemLogger::log(SessionManager::loggedInUid(), SystemLogger::OBJECT_RBAC_ROLE, SystemLogger::ACTION_UPDATE, "Failure", $original_object, $role->expose());
            return new APIResponse("", "Role updation failed.", false);
        }
    }

    /**
     * Get all permissions in the system 
     * 
     * @api
     */
    function rbac_get_permissions()
    {
        $permissions = _rbac_get_permissions();

        $ret = array();

        foreach ($permissions as $permission)
        {
            $ret[] = $permission->expose();
        }

        return new APIResponse($ret);
    }

    /**
     * Get the set of all permissions
     * 
     * @return Array[Permission]
     */
    function _rbac_get_permissions()
    {
        $db = Codeli::getInstance()->getDB();

        $sql = "SELECT * FROM " . DatabaseTables::PERMISSION . " ORDER BY title ASC";

        $results = $db->query($sql);

        $data = array();
        while ($res = $db->fetchObject($results))
        {
            $permission = new Permission();
            $permission->loadFromMap($res);
            $data[$permission->getId()] = $permission;
        }

        return $data;
    }

    /**
     * Add a new permission to a given role
     */
    function rbac_role_add_permission()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!Role::isExistent($data->rid))
        {
            return new APIResponse("", "Invalid Role Id. ", false);
        }
        if (!Permission::isExistent($data->permission))
        {
            return new APIResponse("", "Invalid Permission Id. ", false);
        }

        $db = Codeli::getInstance()->getDB();
        $sql = "INSERT INTO " . DatabaseTables::ROLE_PERMISSION . " (rid, permission) VALUES('::rid', '::permission')";
        $args = array(
            "::rid" => $data->rid,
            "::permission" => $data->permission,
        );

        $res = $db->query($sql, $args);

        if ($res)
        {
            return new APIResponse("", "Role Permission successfully added", true);
        }
        else
        {
            return new APIResponse("", "Role Permission addition failed", false);
        }
    }

    /**
     * Delete a permission from a given role
     */
    function rbac_role_remove_permission()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!Role::isExistent($data->rid))
        {
            return new APIResponse("", "Invalid Role Id. ", false);
        }
        if (!Permission::isExistent($data->permission))
        {
            return new APIResponse("", "Invalid Permission Id. ", false);
        }

        $db = Codeli::getInstance()->getDB();
        $sql = "DELETE FROM " . DatabaseTables::ROLE_PERMISSION .
                " WHERE rid='::rid' AND permission='::permission' LIMIT 1";
        $args = array(
            "::rid" => $data->rid,
            "::permission" => $data->permission,
        );

        $res = $db->query($sql, $args);

        if ($res)
        {
            return new APIResponse("", "Role Permission successfully removed", true);
        }
        else
        {
            return new APIResponse("", "Role Permission removal failed", false);
        }
    }
    
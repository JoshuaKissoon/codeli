<?php

    /**
     * Base module class for the user module
     *
     * @author Joshua Kissoon
     * @since 20150103
     */
    class UserModule implements Module
    {

        private $name = "User Management";
        private $description = "Module that handles all User Management";

        public function bootup()
        {
            $theme = Codeli::getInstance()->getThemeRegistry();
            
            $theme->addCss("SomeCss.css");
        }
        
        public function getDescription()
        {
            return $this->description;
        }

        public function getTitle()
        {
            return $this->name;
        }

        public function getPermissions()
        {
            $permissions = array();

            /* User Permissions */
            $permissions[] = new PermissionInfo("user_user_view", "User: View");
            $permissions[] = new PermissionInfo("user_user_add", "User: Add");
            $permissions[] = new PermissionInfo("user_user_edit", "User: Edit");
            $permissions[] = new PermissionInfo("user_user_delete", "User: Delete");

            $permissions[] = new PermissionInfo("user_role_view", "User: View User Roles");
            $permissions[] = new PermissionInfo("user_role_add", "User: Add User Role");
            $permissions[] = new PermissionInfo("user_role_edit", "User: Edit User Role");

            /* RBAC Permissions */
            $permissions[] = new PermissionInfo("rbac_role_view", "RBAC: View Role");
            $permissions[] = new PermissionInfo("rbac_role_add", "RBAC: Add Role");
            $permissions[] = new PermissionInfo("rbac_role_edit", "RBAC: Edit Role");

            $permissions[] = new PermissionInfo("rbac_permission_view", "RBAC: View Permission");
            $permissions[] = new PermissionInfo("rbac_role_permission_add", "RBAC: Add Role Permission");
            $permissions[] = new PermissionInfo("rbac_role_permission_remove", "RBAC: Delete Role Permission");



            return $permissions;
        }

        public function getRoutes()
        {
            $routes = array();

            /* User Routes */
            $routes[] = new RouteInfo("admin/user", "user_add_user", "user_user_add", HTTP::METHOD_PUT);
            $routes[] = new RouteInfo("admin/user", "user_edit_user", "user_user_edit", HTTP::METHOD_POST);
            $routes[] = new RouteInfo("admin/user/all", "user_get_users", "user_user_view", HTTP::METHOD_POST);
            $routes[] = new RouteInfo("admin/user/%", "user_get_user", "user_user_view", HTTP::METHOD_GET);
            $routes[] = new RouteInfo("admin/user/login", "user_user_login", "", HTTP::METHOD_POST);
            $routes[] = new RouteInfo("admin/user/logout", "user_user_logout", "", HTTP::METHOD_POST);

            /**
             * RBAC Routes
             */
            $routes[] = new RouteInfo("rbac/role", "rbac_role_add", "rbac_role_add", HTTP::METHOD_PUT);
            $routes[] = new RouteInfo("rbac/role/%", "rbac_role_view", "rbac_role_view", HTTP::METHOD_GET);
            $routes[] = new RouteInfo("rbac/role", "rbac_role_edit", "rbac_role_edit", HTTP::METHOD_POST);
            $routes[] = new RouteInfo("rbac/role/all", "rbac_role_view_all", "rbac_role_edit", HTTP::METHOD_POST);
            $routes[] = new RouteInfo("rbac/permission/all", "rbac_get_permissions", "rbac_permission_view", HTTP::METHOD_GET);

            /**
             * User Role Routes 
             */
            $routes[] = new RouteInfo("rbac/role/permission", "rbac_role_add_permission", "rbac_role_permission_add", HTTP::METHOD_PUT);
            $routes[] = new RouteInfo("rbac/role/permission", "rbac_role_remove_permission", "rbac_role_permission_remove", HTTP::METHOD_POST);


            return $routes;
        }

        public function getDependencies()
        {
            return array();
        }

    }
    
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

            $permissions[] = new Permission("view_users", "View Users");
            $permissions[] = new Permission("add_user", "Add User");
            $permissions[] = new Permission("edit_user", "Edit User");
            $permissions[] = new Permission("delete_user", "Delete User");


            return $permissions;
        }

        public function getRoutes()
        {
            $routes = array();

            $routes[] = new Route("admin/user", "user_get_users", "view_users", HTTP::METHOD_GET);

            return $routes;
        }

    }
    
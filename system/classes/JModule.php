<?php

    /**
     * Class that handles all module operations
     * 
     * @author Joshua Kissoon
     * @since 20121218
     */
    class JModule implements DatabaseObject
    {

        private $name;
        private $description;
        private $type;
        private $permissions = array();
        private $isPermissionsLoaded = false;
        private $routes = array();
        private $isRoutesLoaded = false;

        /**
         * If the name is specified, load the module
         */
        public function __construct($modname = null)
        {
            if ($modname)
            {
                $this->load($modname);
            }
        }

        public function getId()
        {
            
        }
        
        public function getPermissions()
        {
            if(!$this->isPermissionsLoaded)
            {
                $this->loadPermissions();
            }
            
            return $this->permissions;
        }
        public function getRoutes()
        {
            if(!$this->isRoutesLoaded)
            {
                $this->loadRoutes();
            }
            
            return $this->routes;
        }

        public static function isExistent($modname)
        {
            $sweia = Codeli::getInstance();
            $db = $sweia->getDB();
            $res = $db->fetchObject($db->query("SELECT name FROM module WHERE name='::modname'", array("::modname" => $modname)));

            if (isset($res->name))
            {
                return ($modname == $res->name) ? true : false;
            }

            return false;
        }

        public function load()
        {
            if (!self::moduleExists($this->modname))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            $mod = $db->fetchObject($db->query("SELECT * FROM module WHERE name='::modname'", array("::modname" => $this->modname)));
            foreach ($mod as $key => $value)
            {
                $this->$key = $value;
            }

            return $this;
        }

        /**
         * Loads an array with the permissions for this module
         */
        private function loadPermissions()
        {
            $sweia = Codeli::getInstance();
            $db = $sweia->getDB();
            $this->permissions = array();
            $perms = $db->query("SELECT * FROM permission WHERE module='::modname'", array("::modname" => $this->name));
            while ($perm = $db->fetchObject($perms))
            {
                $this->permissions[$perm->permission] = $perm->title;
            }
        }

        /**
         * Loads an array with the URLs for this module
         */
        private function loadRoutes()
        {
            $sweia = Codeli::getInstance();
            $db = $sweia->getDB();
            $this->routes = array();
            $urls = $db->query("SELECT * FROM url_handler WHERE module='::modname'", array("::modname" => $this->name));
            while ($url = $db->fetchObject($urls))
            {
                $this->routes[$url->url] = $url;
            }
        }

        /**
         * Add/update a module to the database
         */
        public function save()
        {
            if (!isset($this->name))
            {
                return false;
            }

            if (self::isExistent($this->name))
            {
                return $this->update();
            }
            else
            {
                return $this->add();
            }
        }

        /**
         * Adds a new module to the database
         */
        private function insert()
        {
            $db = Codeli::getInstance()->getDB();
            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "INSERT INTO $this->tbl (name, title, description, type, status) VALUES ('::name', '::title', '::desc', '::type', '::status')";
            $db->query($sql, $values);

            $this->savePermissions();
            $this->saveRoutes();
            return true;
        }

        /**
         * Adds a permission to this module's premission array, this is not yet saved to the DB
         * 
         * @param $perm
         * @param $title
         */
        public function addPermission($perm, $title)
        {
            $this->permissions[$perm] = $title;
        }

        /**
         * Add Module permissions to the database 
         */
        private function savePermissions()
        {
            foreach ($this->permissions as $perm => $title)
            {
                $this->savePermission($perm, $title);
            }
        }

        /**
         * Adds a single permission for a module to the database
         */
        private function savePermission($perm, $title)
        {
            $sweia = Codeli::getInstance();
            $db = $sweia->getDB();

            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "INSERT INTO permission (permission, title, module) VALUES ('::perm', '::title', '::modname')
                ON DUPLICATE KEY UPDATE title = '::title', module = '::modname'";
            $db->query($sql, $values);
        }

        /**
         * Adds a permurlission to this module's urls array, this is not yet saved to the DB
         * 
         * @param $url
         * @param $data
         */
        public function addRoute(Route $route)
        {
            $this->routes[$route->getURL()] = $route;
        }

        /**
         * Add Module urls to the database 
         */
        private function saveRoutes()
        {
            foreach ($this->routes as $route)
            {
                $route->save();
            }
        }

        public function hasMandatoryData()
        {
            
        }

        /**
         * Updates a current module data in the database 
         */
        private function update()
        {
            $db = Codeli::getInstance()->getDB();

            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "UPDATE $this->tbl SET description = '::desc', status = '::status', type = '::type', title = '::title' WHERE name = '::name'";
            $db->query($sql, $values);

            $this->updatePermissions();
            $this->updateRoutes();
            return true;
        }

        /**
         * Update permissions that already exist, and elete module permissions that are in the database but not in the new permission list
         */
        private function updatePermissions()
        {
            /* Load the old permissions */
            $new_perms = $this->permissions;        // Save the current permissions array
            $this->loadPermissions();               // Load the old permissions
            $old_perms = $this->permissions;
            $this->permissions = $new_perms;

            foreach ($this->permissions as $perm => $title)
            {
                /* For each permission, if it already exists, update it. Else, add it to the database */
                if (array_key_exists($perm, $old_perms))
                {
                    $this->updatePermission($perm, $title);
                    unset($old_perms[$perm]);   // Remove permission from old_perms array if it is a part of new permissions
                }
                else
                {
                    $this->savePermission($perm, $title);
                }
            }

            foreach ($old_perms as $perm => $title)
            {
                /* The old permissions array will only contain permissions no longer in use, remove these */
                $this->deletePermission($perm);
            }
        }

        /**
         * Updates a single permission for this module
         */
        private function updatePermission($perm, $title)
        {
            $db = Codeli::getInstance()->getDB();

            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "UPDATE permission SET title = '::title', module = '::modname' WHERE permission = '::perm'";
            $db->query($sql, $values);
        }

        /**
         * Update urls that already exist for this module. Delete module urls that are in the database but not in the new permission list
         */
        private function updateRoutes()
        {
            $this->loadUrls();               // Load the old permissions

            foreach ($this->routes as $route)
            {
                $route->update();
            }
        }

        /**
         * Delete the specified permission from permission table and role_permission table
         */
        private function deletePermission($perm)
        {
            $db = Codeli::getInstance()->getDB();

            $db->query("DELETE FROM role_permission WHERE permission='::perm'", array("::perm" => $perm));
            $db->query("DELETE FROM permission WHERE permission='::perm'", array("::perm" => $perm));
        }

        /**
         * Completely delete this module and all of it's data from the database
         */
        public function delete()
        {
            if (!$this->moduleExists())
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            /* Delete the URLs and Permissions associated with this module */
            $rs = $db->query("SELECT url FROM url_handler WHERE module='::mod'", array("::mod" => $this->name));
            while ($url = $db->fetchObject($rs))
            {
                $this->deleteUrl($url->url);
            }
            $rs2 = $db->query("SELECT * FROM permission WHERE module='::mod'", array("::mod" => $this->name));
            while ($perm = $db->fetchObject($rs2))
            {
                $this->deletePermission($perm->permission);
            }

            /* Delete the module data */
            return $db->query("DELETE FROM module WHERE name='::mod'", array("::mod" => $this->name));
        }

    }
    
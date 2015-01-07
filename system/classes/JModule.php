<?php

    /**
     * Class that handles all module operations
     * 
     * @author Joshua Kissoon
     * @since 20121218
     */
    class JModule implements DatabaseObject
    {

        private $guid;
        private $title;
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
            return $this->guid;
        }

        public function setGuid($guid)
        {
            $this->guid = $guid;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setDescription($desc)
        {
            $this->description = $desc;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function setType($type)
        {
            $this->type = $type;
        }

        public function getType()
        {
            return $this->type;
        }

        public function getPermissions()
        {
            if (!$this->isPermissionsLoaded)
            {
                $this->loadPermissions();
            }

            return $this->permissions;
        }

        public function getRoutes()
        {
            if (!$this->isRoutesLoaded)
            {
                $this->loadRoutes();
            }

            return $this->routes;
        }

        public static function isExistent($guid)
        {
            $db = Codeli::getInstance()->getDB();
            $res = $db->fetchObject($db->query("SELECT guid FROM " . DatabaseTables::MODULE .
                            " WHERE guid='::guid'", array("::guid" => $guid)));

            if (isset($res->guid))
            {
                return ($guid == $res->guid) ? true : false;
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
            $perms = $db->query("SELECT * FROM " . SystemDatabaseTables::PERMISSION . " WHERE module='::modname'", array("::modname" => $this->name));
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
            if (self::isExistent($this->guid))
            {
                return $this->update();
            }
            else
            {
                return $this->insert();
            }
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();
            $values = array(
                "::guid" => $this->guid,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "INSERT INTO " . DatabaseTables::MODULE .
                    " (guid, title, description, type, status) "
                    . " VALUES ('::guid', '::title', '::desc', '::type', '::status') "
                    . " ON DUPLICATE KEY UPDATE title='::title', description='::desc', status='::status'";
            $db->query($sql, $values);

            return true;
        }

        /**
         * Adds a permission to this module's permission array
         * 
         * @param $perm
         */
        public function addPermission(Permission $perm)
        {
            $this->permissions[$perm->getPermission()] = $perm;
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

        public function hasMandatoryData()
        {
            
        }

        /**
         * Updates a current module data in the database 
         */
        public function update()
        {
            $db = Codeli::getInstance()->getDB();

            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "UPDATE " . DatabaseTables::MODULE . " SET description = '::desc', status = '::status', type = '::type', title = '::title' WHERE name = '::name'";
            $db->query($sql, $values);

            return true;
        }

        /**
         * Completely delete this module and all of it's data from the database
         */
        public static function delete($name)
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
    
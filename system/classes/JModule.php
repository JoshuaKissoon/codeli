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
        private $dependencies = array();
        private $isDependenciesLoaded = false;

        /**
         * If the guid is specified, load the module
         * 
         * @param String $guid The module's GUID
         */
        public function __construct($guid = null)
        {
            if (null == $guid)
            {
                return;
            }

            $this->guid = $guid;
            $this->load();
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
                $sweia = Codeli::getInstance();
                $db = $sweia->getDB();
                $this->permissions = array();
                $perms = $db->query("SELECT * FROM " . SystemDatabaseTables::PERMISSION . " WHERE module='::modname'", array("::modname" => $this->name));
                while ($perm = $db->fetchObject($perms))
                {
                    $this->permissions[$perm->permission] = $perm->title;
                }
                $this->isPermissionsLoaded = true;
            }

            return $this->permissions;
        }

        public function getRoutes()
        {
            if (!$this->isRoutesLoaded)
            {
                $sweia = Codeli::getInstance();
                $db = $sweia->getDB();
                $this->routes = array();
                $urls = $db->query("SELECT * FROM url_handler WHERE module='::modname'", array("::modname" => $this->name));
                while ($url = $db->fetchObject($urls))
                {
                    $this->routes[$url->url] = $url;
                }

                $this->isRoutesLoaded = true;
            }

            return $this->routes;
        }

        /**
         * Get the set of dependencies for this module
         */
        public function getDependencies()
        {
            if (!$this->isDependenciesLoaded)
            {
                $db = Codeli::getInstance()->getDB();

                $this->dependencies = array();

                $results = $db->query("SELECT * FROM " . SystemTables::MODULE_DEPENDENCY . " WHERE guid='::guid'", array("::guid" => $this->guid));

                while ($res = $db->fetchObject($results))
                {
                    $this->dependencies[$res->dependencyGuid] = $res;
                }

                $this->isDependenciesLoaded = true;
            }

            return $this->dependencies;
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
            $db = Codeli::getInstance()->getDB();

            $mod = $db->fetchObject($db->query("SELECT * FROM module WHERE guid='::guid'", array("::guid" => $this->guid)));
            foreach ($mod as $key => $value)
            {
                $this->$key = $value;
            }

            return $this;
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

            $this->updateDependencies();

            return true;
        }

        /**
         * Delete all dependencies in the system and re-add them
         */
        public function updateDependencies()
        {
            $db = Codeli::getInstance()->getDB();
            $db->query("DELETE FROM " . SystemTables::MODULE_DEPENDENCY . " WHERE guid='::guid'", array("::guid" => $this->guid));

            if (count($this->dependencies) < 1)
            {
                return;
            }

            $values = array();
            foreach ($this->dependencies as $dep => $data)
            {
                $values[] = " ('$this->guid', '$dep') ";
            }

            $sql = "INSERT INTO " . SystemTables::MODULE_DEPENDENCY . " (guid, dependencyGuid) VALUES " . implode(",", $values);
            return $db->query($sql);
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

        /**
         * Adds a permurlission to this module's urls array, this is not yet saved to the DB
         * 
         * @param $url
         * @param $data
         */
        public function addDependency($dependency)
        {
            $this->dependencies[$dependency] = array("dependencyGuid" => $dependency);
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
                "::guid" => $this->guid,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "UPDATE " . DatabaseTables::MODULE .
                    " SET description = '::desc', status = '::status', type = '::type', title = '::title' WHERE guid = '::guid'";
            $db->query($sql, $values);

            $this->updateDependencies();

            return true;
        }

        /**
         * Completely delete this module and all of it's data from the database
         */
        public static function delete($guid)
        {
            if (!self::isExistent($guid))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            /* Delete the Routes associated with this module */
            $db->query("DELETE FROM route WHERE module='::guid'", array("::guid" => $this->guid));

            /* Delete the Permissions associated with this module */
            $db->query("DELETE FROM permission WHERE module='::guid'", array("::guid" => $this->guid));

            /* Delete the Module Dependencies associated with this module */
            $db->query("DELETE FROM " . SystemTables::MODULE_DEPENDENCY . " WHERE guid='::guid'", array("::guid" => $this->guid));

            /* Delete the module data */
            return $db->query("DELETE FROM " . SystemTables::MODULE . " WHERE guid='::guid'", array("::guid" => $this->guid));
        }

    }
    
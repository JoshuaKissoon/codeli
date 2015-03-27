<?php

    /**
     * A class that handles user roles
     * 
     * @author Joshua Kissoon
     * @since 20130316
     * @updated 20140623
     */
    class Role implements DatabaseObject
    {

        private $rid;
        private $title;
        private $description;

        /* Permissions management */
        private $permissions = array();
        private $isPermissionsLoaded = false;

        public function __construct($rid = null)
        {
            if (self::isExistent($rid))
            {
                $this->rid = $rid;
                return $this->load();
            }
        }

        public function getId()
        {
            return $this->rid;
        }

        public function setTitle($var)
        {
            $this->title = $var;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setDescription($var)
        {
            $this->description = $var;
        }

        public static function isExistent($rid)
        {
            $db = Codeli::getInstance()->getDB();

            $res = $db->query("SELECT rid FROM " . SystemTables::ROLE . " WHERE rid = '::rid'", array("::rid" => $rid));
            $role = $db->fetchObject($res);
            return (isset($role->rid) && valid($role->rid)) ? true : false;
        }

        public function hasMandatoryData()
        {
            
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                '::title' => $this->title,
                '::description' => $this->description,
            );
            $sql = "INSERT INTO " . SystemTables::ROLE . " (title, description) VALUES ('::title', '::description')";

            $res = $db->query($sql, $args);

            if (!$res)
            {
                return false;
            }

            $this->rid = $db->lastInsertId();
            return true;
        }

        public function update()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                '::rid' => $this->rid,
                '::title' => $this->title,
                '::description' => $this->description,
            );
            $sql = "UPDATE " . SystemTables::ROLE . " SET title='::title', description='::description' WHERE rid=::rid LIMIT 1";

            return $db->query($sql, $args);
        }

        public function load()
        {
            $db = Codeli::getInstance()->getDB();

            $result = $db->query("SELECT * FROM " . SystemTables::ROLE . " WHERE rid='::rid' LIMIT 1", array("::rid" => $this->rid));

            if ($db->resultNumRows() != 1)
            {
                return false;
            }

            $res = $db->fetchObject($result);

            $this->loadFromMap($res);
            return true;
        }

        public function loadFromMap($data)
        {
            $this->rid = $data->rid;
            $this->title = $data->title;
            $this->description = $data->description;
        }

        public static function delete($rid)
        {
            if (!self::isExistent($rid))
            {
                return false;
            }

            /* Remove this role from all user's and permissions and then delete all of it's data */
            $db = Codeli::getInstance()->getDB();

            $args = array("::rid" => $rid);
            if ($db->query("DELETE FROM " . SystemTables::USER_ROLE . " WHERE rid = '::rid'", $args))
            {
                $db->query("DELETE FROM " . SystemTables::ROLE_PERMISSION . " WHERE rid = '::rid'", $args);
                if ($db->query("DELETE FROM " . SystemTables::ROLE . " WHERE rid = '::rid'", $args))
                {
                    return true;
                }
            }
            return false;
        }

        /**
         * Adds a new permission to this role
         * 
         * @return Boolean - Whether the new permission was successfully added
         */
        public function addAndSavePermission($perm)
        {
            $db = Codeli::getInstance()->getDB();

            /* Check if this is a valid permission */
            $res = $db->fetchObject($db->query("SELECT permission FROM permission WHERE permission='::perm'", array("::perm" => $perm)));
            if (!valid($res->permission))
            {
                return false;
            }

            /* It is a valid permission, so now we add it to the role */
            $this->permissions[$perm] = $perm;
            return true;
        }

        /**
         * Load all permissions for this role from the database
         * 
         * @return Integer - The number of permissions that are associated with this role
         */
        public function loadPermissions()
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT rp.permission, p.* FROM " . SystemTables::ROLE_PERMISSION .
                    " rp LEFT JOIN " . DatabaseTables::PERMISSION . " p ON (rp.permission = p.permission) WHERE rid = '::rid'";
            $res = $db->query($sql, array("::rid" => $this->rid));

            while ($perm = $db->fetchObject($res))
            {
                $permission = new Permission();
                $permission->loadFromMap($perm);
                $this->permissions[$permission->getId()] = $permission;
            }

            $this->isPermissionsLoaded = true;
            return count($this->permissions);
        }

        /**
         * @return Array[Permission]
         */
        public function getPermissions()
        {
            if (!$this->isPermissionsLoaded)
            {
                $this->loadPermissions();
            }

            return $this->permissions;
        }

        /**
         * Checks if a role has a permission 
         * 
         * @param $perm The permission to check for
         * 
         * @return Boolean - Whether the role has the permission or not
         */
        public function hasPermission($perm)
        {
            return (array_key_exists($perm, $this->permissions)) ? true : false;
        }

        /**
         * Method that returns an exposed version of the class's data
         */
        public function expose()
        {
            $obj = clone $this;

            $obj->permissions = array();

            foreach ($this->getPermissions() as $perm)
            {
                $obj->permissions[] = $perm->expose();
            }

            return get_object_vars($obj);
        }

    }
    
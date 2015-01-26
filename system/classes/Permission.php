<?php

    /**
     * A representation of a permission
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    class Permission implements DatabaseObject
    {

        private $pid = 0;
        private $permission;
        private $title;
        private $description;

        /**
         * Create a new permission instance
         * 
         * @param $permission The permission
         * @param $title The title of this permission
         */
        public function __construct($permission = null)
        {
            if (null != $permission)
            {
                $this->permission = $permission;
                $this->load();
            }
        }

        public function getId()
        {
            return $this->pid;
        }

        public function setPermission($val)
        {
            $this->permission = $val;
        }

        /**
         * @return String - The permission identifier
         */
        public function getPermission()
        {
            return $this->permission;
        }

        public function setTitle($val)
        {
            $this->title = $val;
        }

        /**
         * @return String - The title of this permission
         */
        public function getTitle()
        {
            return $this->title;
        }

        /**
         * Set the permission's description
         * 
         * @param $description
         */
        public function setDescription($description)
        {
            $this->description = $description;
        }

        /**
         * @return String - The description of this permission
         */
        public function getDescription()
        {
            return $this->description;
        }

        public function hasMandatoryData()
        {
            
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();

            $values = array(
                '::perm' => $this->permission,
                '::title' => $this->title,
                '::description' => $this->description
            );
            $sql = "INSERT INTO " . DatabaseTables::PERMISSION .
                    " (permission, title, description) VALUES ('::perm', '::title', '::description')
                ON DUPLICATE KEY UPDATE title = '::title', description = '::description'";

            $res = $db->query($sql, $values);

            if (!$res)
            {
                return false;
            }

            $this->pid = $db->lastInsertId();
            return true;
        }

        public function load()
        {
            if (null == $this->permission || "" == $this->permission)
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT * FROM " . DatabaseTables::PERMISSION . " WHERE permission='::permission' LIMIT 1";
            $args = array("::permission" => $this->permission);
            $res = $db->query($sql, $args);
            $data = $db->fetchObject($res);

            $this->loadFromMap($data);

            return true;
        }

        public function loadFromMap($data)
        {
            $this->pid = $data->pid;
            $this->permission = $data->permission;
            $this->title = $data->title;
            $this->description = $data->description;
        }

        public function update()
        {
            
        }

        public static function delete($id)
        {
            
        }

        public static function isExistent($perm = "", $pid = "")
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT * FROM " . DatabaseTables::PERMISSION;
            if ("" === $pid)
            {
                $sql .= " WHERE permission='::permission' LIMIT 1";
                $args = array("::permission" => $perm);
            }
            else
            {
                $sql .= " WHERE pid='::pid' LIMIT 1";
                $args = array("::pid" => $pid);
            }

            $res = $db->query($sql, $args);

            return ($db->resultNumRows($res) == 1);
        }

        /**
         * Method that returns an exposed version of the class's data
         */
        public function expose()
        {
            return get_object_vars($this);
        }

        public function __toString()
        {
            $obj = $this;
            return $this->expose();
        }

    }
    
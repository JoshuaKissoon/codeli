<?php

    /**
     * A representation of a permission
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    class Permission implements DatabaseObject
    {

        private $permission;
        private $title;
        private $description;
        private $module;

        /**
         * Create a new permission instance
         * 
         * @param $permission The permission
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
            return $this->permission;
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

        public function setModule($val)
        {
            $this->module = $val;
        }

        /**
         * @return String - The title of this permission
         */
        public function getModule()
        {
            return $this->module;
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
                '::description' => $this->description,
                '::module' => $this->module,
            );
            $sql = "INSERT INTO " . DatabaseTables::PERMISSION .
                    " (permission, title, description, module) VALUES ('::perm', '::title', '::description', '::module')
                ON DUPLICATE KEY UPDATE title = '::title', description = '::description', module='::module'";

            $res = $db->query($sql, $values);

            if (!$res)
            {
                return false;
            }

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
            if ($data->permission == "")
            {
                print $this->permission;
                exit;
            }
            $this->permission = $data->permission;
            $this->title = $data->title;
            $this->description = $data->description;
            $this->module = $data->module;
        }

        public function update()
        {
            $db = Codeli::getInstance()->getDB();

            $values = array(
                '::perm' => $this->permission,
                '::title' => $this->title,
                '::description' => $this->description,
                '::module' => $this->module,
            );
            $sql = "UPDATE " . DatabaseTables::PERMISSION .
                    " SET title = '::title', description = '::description', module='::module' WHERE permission='::perm' LIMIT 1";

            $res = $db->query($sql, $values);

            if (!$res)
            {
                return false;
            }

            return true;
        }

        public function save()
        {
            if (self::isExistent($this->permission))
            {
                $this->update();
            }
            else
            {
                $this->insert();
            }
        }

        public static function delete($id)
        {
            
        }

        public static function isExistent($permission)
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT * FROM " . DatabaseTables::PERMISSION;
            $sql .= " WHERE permission='::permission' LIMIT 1";
            $args = array("::permission" => $permission);

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
            return $this->expose();
        }

    }
    
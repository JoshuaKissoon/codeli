<?php

    /**
     * A representation of a permission
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    class Permission implements DatabaseObject
    {

        private $pid;
        private $permission;
        private $title;
        private $description;

        /**
         * Create a new permission instance
         * 
         * @param $permission The permission
         * @param $title The title of this permission
         */
        public function __construct($permission, $title)
        {
            $this->permission = $permission;
            $this->title = $title;
        }

        /**
         * @return String - The permission identifier
         */
        public function getPermission()
        {
            return $this->permission;
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

        public function getId()
        {
            return $this->pid;
        }

        public function hasMandatoryData()
        {
            
        }

        public function insert()
        {
            $sweia = Codeli::getInstance();
            $db = $sweia->getDB();

            $values = array(
                '::perm' => $this->permission,
                '::title' => $this->title,
                '::description' => $this->description
            );
            $sql = "INSERT INTO permission (permission, title, module) VALUES ('::perm', '::title', '::description')
                ON DUPLICATE KEY UPDATE title = '::title', description = '::description'";
            $db->query($sql, $values);
        }

        public function load()
        {
            
        }

        public function update()
        {
            
        }

        public static function delete($id)
        {
            
        }

        public static function isExistent($id)
        {
            
        }

    }
    
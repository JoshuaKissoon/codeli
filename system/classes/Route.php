<?php

    /**
     * Class representing a route handled by a module
     *
     * @author Joshua Kissoon
     * @since 20150103
     */
    class Route implements DatabaseObject
    {

        private $rid;   // Unique ID for this route
        private $url;   // Which URL does this route handle
        private $callback;  // Callback function for this route
        private $module;    // Which module is handling this request
        private $permission;    // Which permission is required to access this route
        private $method;    // What method does this route handle

        public function __construct()
        {
            
        }

        public function getURL()
        {
            return $this->url;
        }

        public function setCallback($callback)
        {
            $this->callback = $callback;
        }

        public function getCallback()
        {
            return $this->callback;
        }

        public function getModule()
        {
            return $this->module;
        }

        public function getPermissionId()
        {
            return $this->permission;
        }

        public function getHTTPMethod()
        {
            return $this->method;
        }

        public function setId($id)
        {
            $this->rid = $id;
        }

        public function getId()
        {
            return $this->rid;
        }

        public function setData($url, $callback, $module, $permission, $httpMethod = HTTP::METHOD_GET)
        {
            $this->url = $url;
            $this->callback = $callback;
            $this->module = $module;
            $this->permission = $permission;
            $this->method = $httpMethod;
        }

        public function importData($data)
        {
            $this->rid = $data->rid;
            $this->url = $data->url;
            $this->callback = $data->callback;
            $this->module = $data->module;
            $this->permission = $data->permission;
            $this->method = $data->method;
        }

        public static function isExistent($id)
        {
            
        }

        public function hasMandatoryData()
        {
            if ("" == $this->url || null == $this->url)
            {
                return false;
            }
            if ("" == $this->callback || null == $this->callback)
            {
                return false;
            }
            if ("" == $this->permission || null == $this->permission)
            {
                return false;
            }

            return true;
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                '::url' => $this->url,
                '::module' => $this->module,
                '::permission' => $this->permission,
                '::callback' => $this->callback,
                '::method' => $this->method,
            );
            $sql = "INSERT INTO " . DatabaseTables::ROUTE .
                    " (url, module, permission, callback, method) "
                    . "VALUES('::url', '::module', '::permission', '::callback', '::method') "
                    . " ON DUPLICATE KEY UPDATE permission='::permission', callback='::callback'";

            $res = $db->query($sql, $args);
            if (!$res)
            {
                return false;
            }

            $this->rid = $db->lastInsertId();
            return true;
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

        /**
         * Method that returns an exposed version of the class's data
         */
        public function expose()
        {
            $object = get_object_vars($this);
            return $object;
        }

        public function loadFromMap($data)
        {
            
        }

    }
    
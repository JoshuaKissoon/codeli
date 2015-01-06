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
        private $httpMethod;    // What method does this route handle

        public function __construct($url, $callback, $module, $permission = "", $httpMethod = HTTP::METHOD_GET)
        {
            $this->url = $url;
            $this->callback = $callback;
            $this->module = $module;
            $this->permission = $permission;
            $this->httpMethod = $httpMethod;
        }

        public function getURL()
        {
            return $this->url;
        }

        public function getCallback()
        {
            return $this->callback;
        }

        public function getModule()
        {
            return $this->module;
        }

        public function getPermission()
        {
            return $this->permission;
        }

        public function getHTTPMethod()
        {
            return $this->httpMethod;
        }

        public function getId()
        {
            return $this->rid;
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
                '::method' => $this->httpMethod,
            );
            $sql = "INSERT INTO " . DatabaseTables::ROUTE . " (url, module, permission, callback, method) VALUES('::url', '::module', '::permission', '::callback', '::method'";

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

    }
    
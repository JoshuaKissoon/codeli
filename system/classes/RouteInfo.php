<?php

    /**
     * A class that contains basic information of a route
     *
     * @author Joshua Kissoon
     * @since 20150106
     */
    class RouteInfo
    {

        private $url;   // Which URL does this route handle
        private $callback;  // Callback function for this route
        private $permission;    // Which permission is required to access this route
        private $httpMethod;    // What method does this route handle

        public function __construct($url, $callback, $permission, $httpMethod = HTTP::METHOD_GET)
        {
            $this->url = $url;
            $this->callback = $callback;
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

        public function getPermission()
        {
            return $this->permission;
        }

        public function getMethod()
        {
            return $this->httpMethod;
        }

    }
    
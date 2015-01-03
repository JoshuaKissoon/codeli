<?php

    /**
     * Class representing a route handled by a module
     *
     * @author Joshua Kissoon
     * @since 20150103
     */
    class Route
    {

        private $url;
        private $callback;
        private $permission;
        private $httpMethod;

        public function __construct($url, $callback, $permission = "", $httpMethod = "GET")
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

        public function getHTTPMethod()
        {
            return $this->httpMethod;
        }

    }
    
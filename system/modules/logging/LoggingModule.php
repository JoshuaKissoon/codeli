<?php

    /**
     * Module that provides logging functionality to the rest of the system
     *
     * @author Joshua Kissoon
     * @since 20160128
     */
    class LoggingModule implements Module
    {
        private $name = "Logging";
        private $description = "Module that provides logging functionality to the rest of the system";
        
        
        public function bootup()
        {
            
        }

        public function getTitle()
        {
            return $this->name;
        }

        public function getDescription()
        {
            return $this->description;
        }
        
        public function getDependencies()
        {
            return array();
        }

        public function getPermissions()
        {
            return array();
        }

        public function getRoutes()
        {
            return array();
        }

    }
    
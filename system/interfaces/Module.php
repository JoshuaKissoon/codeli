<?php

    /**
     * Interface specifying the structure of a module
     * 
     * This class provides information about a module.
     *
     * @author Joshua Kissoon
     * @since 20150104
     */
    interface Module
    {
        
        /**
         * This is a method that is called at the initial page load
         * 
         * This method is good for allowing a module to load it's custom scripts or css files
         * 
         * @note This method is not executed when an API call is made
         * 
         * @author Joshua Kissoon
         * @since 20150330
         */
        public function bootup();

        /**
         * @return String - The unique name for this module
         */
        public function getTitle();

        /**
         * @return String - A short description of this module
         */
        public function getDescription();

        /**
         * Get the set of URLs that the module handles
         * 
         * @return Array[RouteInfo] An array of ModuleUrls handled by the module
         */
        public function getRoutes();

        /**
         * Get the set of permissions added by this module
         * 
         * @return Array[PermissionInfo] An array of Permission handled by this module
         */
        public function getPermissions();

        /**
         * Get the set of dependencies for a module
         * 
         * @return Array[String] An array of Module names that this module is dependent on
         */
        public function getDependencies();
    }
    
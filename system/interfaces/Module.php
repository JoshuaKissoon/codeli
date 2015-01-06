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
         * @return Array[Route] An array of ModuleUrls handled by the module
         */
        public function getRoutes();

        /**
         * Get the set of permissions added by this module
         * 
         * @return Array[Permission] An array of Permission handled by this module
         */
        public function getPermissions();
    }
    
<?php

    /**
     * Class that contain methods to help the module class
     *
     * @author Joshua Kissoon
     * @since 20150105
     */
    class ModuleHelper
    {

        /**
         * Get the name of the module's main class
         * 
         * @param String $modname The name of the module
         */
        public static function getModuleClassName($modname)
        {
            return ucfirst($modname) + "Module";
        }

    }
    
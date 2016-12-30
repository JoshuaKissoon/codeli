<?php

    /**
     * Class that handles all management operations on module
     * 
     * @author Joshua Kissoon
     * @since 20121219
     */
    class JModuleManager
    {

        /**
         * Load the main module handler file for this module: modulename.php
         * 
         * @param String $guid The GUID of the module to load
         */
        public static function getModule($guid)
        {
            return self::getModulePath($guid) . "$guid.php";
        }

        /**
         * Get the folder path for the module
         */
        public static function getModulePath($modname)
        {
            $db = Codeli::getInstance()->getDB();

            $temp = $db->fetchObject($db->query("SELECT type FROM " . SystemTables::MODULE . " WHERE guid = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                return SystemConfig::modulesPath() . "$modname/";
            }
            else
            {
                return SiteConfig::modulesPath() . "$modname/";
            }
        }

        /**
         * Here we simply load the main module handler file for this module: modulename.php
         * 
         * @param $modname The module for which to get it's URL
         */
        public static function getModuleURL($modname)
        {
            $db = Codeli::getInstance()->getDB();

            $temp = $db->fetchObject($db->query("SELECT type FROM " . SystemTables::MODULE . " WHERE guid = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                $path = SystemConfig::modulesUrl() . "$modname/";
            }
            else
            {
                $path = SiteConfig::modulesUrl() . "$modname/";
            }
            return $path;
        }

        /**
         * Return a list of all modules within the system
         */
        public static function getModules()
        {
            $db = Codeli::getInstance()->getDB();

            $ret = array();
            $res = $db->query("SELECT * FROM module");
            while ($mod = $db->fetchObject($res))
            {
                $ret[$mod->name] = $mod;
            }
            return $ret;
        }

        /**
         * Scan the modules path for all modules, check for module changes and do updates on site and system modules
         */
        public static function setupModules()
        {
            $current_modules = array();   // Stores all the modules that are currently in the site

            /* Setup system modules */
            $sys_modtype = "system";
            $sys_modules = self::scanModulesDir(SystemConfig::modulesPath());
            foreach ($sys_modules as $modname => $data)
            {
                if (self::setupModule($modname, $sys_modtype))
                {
                    $current_modules[] = $modname;
                }
            }

            /* Setup site modules */
            $site_modtype = "site";
            $site_modules = self::scanModulesDir(SiteConfig::modulesPath());
            foreach ($site_modules as $modname => $data)
            {
                if (self::setupModule($modname, $site_modtype))
                {
                    $current_modules[] = $modname;
                }
            }

            /* Remove the modules that are in the database but no longer on the site */
            self::deleteNullModules($current_modules);
        }

        /**
         * Scan a specified modules directory for modules 
         * 
         * @param The module directory to scan
         * 
         * @return Array - An array of all modules in the given directory
         */
        public static function scanModulesDir($dir)
        {
            if (!is_dir($dir))
            {
                return false;
            }

            $modules = array();
            foreach (new DirectoryIterator($dir) as $fileinfo)
            {
                /* Scan the module directory for modules */
                if ($fileinfo->isDot())
                {
                    continue;
                }

                $guid = $fileinfo->getFilename();
                $modpath = $dir . "$guid/";

                if (!is_dir($modpath))
                {
                    continue;
                }

                $classname = ModuleHelper::getModuleClassName($guid);

                if (!file_exists($modpath . "/$classname.php") || !file_exists($modpath . "/$guid.php"))
                {
                    continue;
                }

                /* All tests were passed, lets add this as a module */
                $modules[$guid] = array("guid" => $guid);
            }
            return $modules;
        }

        /**
         * Load the module data from the module file into the database
         * 
         * @param $modname The name of the module to setup
         * @param $modpath Where is the module located
         * @param $modtype Whether it's a site or system module
         */
        public static function setupModule($guid, $modtype)
        {
            $classname = ModuleHelper::getModuleClassName($guid);

            /**
             * @var Module Get an instance of the module info class
             */
            require_once SystemConfig::basePath() . "$modtype/modules/$guid/$classname.php";
            $modinfo = new $classname;

            /* Only add the module to the site if it has a name */
            $module = new JModule();
            $module->setGuid($guid);
            $module->setTitle($modinfo->getTitle());
            $module->setType($modtype);
            $module->setDescription($modinfo->getDescription());

            /* Adding the permissions */
            foreach ($modinfo->getPermissions() as $perm_info)
            {
                $permission = new Permission();
                $permission->setPermission($perm_info->getPermission());
                $permission->setTitle($perm_info->getTitle());
                $permission->setDescription($perm_info->getDescription());
                $permission->setModule($module->getId());
                $permission->save();
                $module->addPermission($permission);
            }

            /* Adding the Routes for this module */
            foreach ($modinfo->getRoutes() as $rinfo)
            {
                $permission = new Permission($rinfo->getPermission());
                $permission->load();
                $route = new Route();
                $route->setData($rinfo->getURL(), $rinfo->getCallback(), $guid, $permission->getId(), $rinfo->getMethod());
                $route->insert();
                $module->addRoute($route);
            }

            if (count($modinfo->getDependencies()) > 0)
            {
                foreach ($modinfo->getDependencies() as $dependency)
                {
                    $module->addDependency($dependency);
                }
            }

            return $module->save();
        }

        /**
         * Removes from the database all modules not in the current modules list
         * 
         * @param $currentmods A list of current modules
         */
        public static function deleteNullModules($currentmods = array())
        {
            $db = Codeli::getInstance()->getDB();

            $currentmods = "'" . implode("', '", $currentmods) . "'";

            $sql = "SELECT guid FROM " . SystemTables::MODULE .
                    " WHERE guid NOT IN ($currentmods)";

            $rs = $db->query($sql);

            while ($modname = $db->fetchObject($rs))
            {
                /* Delete all modules that are not in the set of current modules */
                $mod = new JModule($modname->name);
                $mod->delete();
            }
        }

        /**
         * Get the list of all active modules in the system
         * 
         * @return Array[JModule]
         */
        public static function getActiveModules()
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT guid FROM " . SystemTables::MODULE .
                    " WHERE status=1";

            $rs = $db->query($sql);

            $modules = array();
            while ($modname = $db->fetchObject($rs))
            {
                $modules[] = new JModule($modname->guid);
            }

            return $modules;
        }

    }
    
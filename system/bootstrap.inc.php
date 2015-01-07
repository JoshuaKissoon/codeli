<?php

    /**
     * Does the initial bootstrap operations for the site
     */
    /* Require the configuration files */
    require_once 'site/includes/BaseConfig.php';
    require_once 'utilities/SystemConfig.php';
    require_once 'site/utilities/SiteConfig.php';

    /* Autoloader for classes and interfaces */
    spl_autoload_register("codeli_load_system_classes");
    spl_autoload_register("codeli_load_system_interfaces");
    spl_autoload_register("codeli_load_system_exceptions");
    spl_autoload_register("codeli_load_site_classes");

    /**
     * Auto loader function to load system classes
     */
    function codeli_load_system_classes($class)
    {
        $file = SystemConfig::classesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }

        $file = SystemConfig::includesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }

        $file = SystemConfig::utilitiesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function codeli_load_system_interfaces($interface)
    {
        $file = SystemConfig::interfacesPath() . $interface . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function codeli_load_system_exceptions($name)
    {
        $file = SystemConfig::exceptionsPath() . $name . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function codeli_load_site_classes($class)
    {
        $file = SiteConfig::classesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    /* Load System Files & Classes */
    require_once SystemConfig::includesPath() . 'functions.inc.php';
    require_once SiteConfig::themePath() . 'Theme.php';

    /* Get an instance of the Sweia object */
    $codeli = Codeli::getInstance();
    $codeli->bootstrap();

    /**
     * @section Testing the database connectivity
     */
    if (!$codeli->getDB()->tryConnect())
    {
        die("Database connectivity error, please check the database access details");
    }

    /* Load the core site and system files */
    require_once SystemConfig::includesPath() . 'system.inc.php';
    require_once SiteConfig::includesPath() . 'site.inc.php';
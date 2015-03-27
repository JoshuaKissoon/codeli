<?php

    /**
     * A class containing constants for the Database tables used by the system.
     * 
     * A class is not exactly required for this situation, that is why it's in the includes folder.
     * 
     * The class is used to provide proper scope to the constants and not let them go wild west in the global scope...
     *
     * @author Joshua Kissoon
     * @since 20140624
     */
    class SystemTables
    {

        /**
         * User management tables
         */
        const USER = "user";
        const USER_V = "user_v";
        const PERMISSION = "permission";
        const USER_STATUS = "user_status";
        const USER_ROLE = "user_role";
        const USER_SESSION = "user_session";

        /**
         * RBAC Tables 
         */
        const ROLE = "role";
        const ROLE_PERMISSION = "role_permission";

        /**
         * Module Tables
         */
        const MODULE = "module";
        const MODULE_DEPENDENCY = "module_dependency";
        const ROUTE = "route";

    }
    
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
        const DB_TBL_USER            = "user";
        const DB_TBL_USER_STATUS     = "user_status";
        const DB_TBL_USER_ROLE       = "user_role";
        const DB_TBL_ROLE            = "role";
        const DB_TBL_ROLE_PERMISSION = "role_permission";

        /**
         * Resource Management tables
         */
        const DB_TBL_USER_SESSION      = "user_session";
        const DB_TBL_ATTENDANCE        = "attendance";
        const DB_TBL_CONNECTION        = "connection";
        const DB_TBL_CONNECTION_LOG    = "connection_log";
        const DB_TBL_CONNECTION_STATUS = "connection_status";
        const DB_TBL_CONNECTION_TYPE   = "connection_type";
        const DB_TBL_CUSTOMER          = "customer";
        const DB_TBL_METER_READING     = "meter_reading";
        const DB_TBL_METER_READING_LOG = "meter_reading_log";
        const DB_TBL_PAYMENT           = "payment";
        const DB_TBL_PAYMENT_LOG       = "payment_log";
        const DB_TBL_PAYMENT_MODE      = "payment_mode";
        const DB_TBL_PAYMENT_STATUS    = "payment_status";
        const DB_TBL_PAYMENT_TYPE      = "payment_type";
        const DB_TBL_EMPLOYEE          = "employee";
        /*
         * API Endpoint tables
         */
        const DB_TBL_API_ENDPOINT      = "api_endpoint";
        const DB_TBL_API_PERMISSION    = "api_permission";

    }
    
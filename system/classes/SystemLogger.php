<?php

    /**
     * Class that handles logging
     *
     * @author Joshua Kissoon
     */
    class SystemLogger
    {

        /**
         * Action Constants
         */
        const ACTION_INSERT = 1;
        const ACTION_UPDATE = 2;
        const ACTION_DELETE = 3;
        const ACTION_VIEW = 4;
        const ACTION_LOGIN = 5;
        const ACTION_LOGOUT = 6;
        const ACTION_COMPUTATON = 7;
        const ACTION_ACTIVATE = 8;
        const ACTION_CANCEL = 9;
        const ACTION_REVIEW = 10;

        /**
         * Object Constants
         */
        const OBJECT_USER = "user";
        const OBJECT_USER_ROLE = "user_role";
        const OBJECT_RBAC_ROLE = "rbac_role";
        const OBJECT_RBAC_PERMISSION = "rbac_permission";

        /**
         * Insert a new Activity log into the database
         * 
         * @todo - Run this operation in a new thread
         */
        public static function log($activityLog)
        {
            $activityLog->insert();
        }

    }
    
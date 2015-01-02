<?php

    /**
     * Base Configuration to be entered by the webmaster
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class BaseConfig
    {

        /** Is the site in a specific folder within your web directory */
        const SITE_FOLDER = "Codeli";

        /* Home URL */
        const HOME_URL = "home";

        /* Database Access Information */
        const DB_SERVER = "localhost";
        const DB_USER = "codeli";
        const DB_PASS = "Pass1233~";
        const DB_NAME = "codeli";

        /* Themes Information */
        const THEME = "default";
        const ADMIN_THEME = "default";

        /* Value used to as a salt when hashing passwords */
        const PASSWORD_SALT = "K<47`5n9~8H5`*^Ks.>ie5&";

        /**
         * Files directory and whether the given directory is relative to the base directory of the system.
         * 
         * These constants can be changed if we're using a CDN later, or a separate files server
         * 
         * @note Exclude leading and trailing slashes
         */
        const FILES_DIR = "files";
        const FILES_DIR_RELATIVE = true;

        /**
         * The URL for files and whether the URL is relative to the base directory of the system
         * 
         * These constants can be changed if we're using a CDN later, or a separate files server
         * 
         * @note Exclude leading and trailing slashes
         */
        const FILES_URL = "files";
        const FILES_URL_RELATIVE = true;

    }
    
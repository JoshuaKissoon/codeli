<?php

    /**
     * Sweia  is the Registry class for all system objects.
     * 
     * @author Joshua Kissoon
     * @since 20121214
     * @updated 20140616
     */
    class Codeli
    {

        private static $instance = null;

        /* Database Object */
        private $DB;
        private $URL;
        private $user = null;

        /**
         * Main class constructor private
         */
        private function __construct()
        {
            $this->DB = new SQLiDatabase();
            $this->URL = JPath::urlArgs();
        }

        /**
         * @return Codeli - an instance of Sweia
         */
        public static function getInstance()
        {
            if (self::$instance == null)
            {
                self::$instance = new Codeli();
            }

            return self::$instance;
        }

        /**
         * Run necessary bootstrap operations in the entire system
         */
        public function bootstrap()
        {
            SessionManager::init();        // Initialize the session
        }

        /**
         * Get the instance of the Database and return it
         * 
         * @return Database Instance of the Database
         */
        public function getDB()
        {
            return $this->DB;
        }

        /**
         * @return The URL object[] with the different arguments of the URL
         */
        public function getURL()
        {
            return $this->URL;
        }

        /**
         * Method used to set the global user object
         */
        public function setUser($user)
        {
            $this->user = $user;
        }

        /**
         * @return JUser - The user object of the logged in system user
         */
        public function getUser()
        {
            if (null == $this->user)
            {
                $this->user = new JUser(SessionManager::loggedInUid());
            }
            return $this->user;
        }

    }
    
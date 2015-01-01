<?php

    /**
     * Manages the current php session
     * 
     * @author Joshua Kissoon
     * @since 20121212
     * @updated 20140616
     */
    class Session
    {

        /**
         * Initialize the session
         */
        public static function init()
        {
            session_start();

            /* Load user data from cookies if the user is not logged in */
            if (!Session::isLoggedIn())
            {
                /* If the user is not logged in, try loading the session and login data from cookies */
                Session::loadDataFromCookies();
            }
        }

        /**
         * Destroy the current session 
         */
        public static function destroy()
        {
            session_destroy();
        }

        /**
         * Creates a new session and logs in a user
         * 
         * @param User The user to log in
         */
        public static function loginUser(User $user)
        {
            session_regenerate_id(true);
            $_SESSION['uid'] = $user->getId();
            $_SESSION['logged_in'] = true;
            $_SESSION['logged_in_email'] = $user->getEmail();

            /* Add the necessary data to the class */
            $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['status'] = 1;

            /* Now we create the necessary cookies for the user and save the session data */
            setcookie("jsmartsid", session_id(), time() + 3600 * 300, "/");

            /* Save the entire session data to the database */
            $args = array(
                "::uid" => $_SESSION['uid'],
                "::sid" => session_id(),
                "::ipaddress" => $_SESSION['ipaddress'],
                "::status" => $_SESSION['status'],
                "::data" => json_encode($_SESSION),
            );

            /* Save the session data to the database */
            $db = Codeli::getInstance()->getDB();
            $db->query("INSERT INTO " . SystemTables::DB_TBL_USER_SESSION . " (uid, sid, ipaddress, status, data) VALUES('::uid', '::sid', '::ipaddress', '::status', '::data')", $args);
        }

        /**
         * Try to load the user's data from cookies 
         * 
         * @return Boolean whether the load was successful or not
         */
        public static function loadDataFromCookies()
        {
            if (!isset($_COOKIE['jsmartsid']))
            {
                return false;
            }

            /* If there is a cookie, check if there exists a valid database session and load it */
            $db = Codeli::getInstance()->getDB();

            $res = $db->query("SELECT * FROM " . SystemTables::DB_TBL_USER_SESSION . " WHERE sid='::sid' LIMIT 1", array("::sid" => $_COOKIE['jsmartsid']));
            if ($db->resultNumRows() < 1)
            {
                /* The session is non-existent, delete it */
                self::invalidateSessionCookie();
                return false;
            }

            /* Session is existent, lets get it's data */
            $row = $db->fetchObject($res);
            if ($row->status != 1)
            {
                /* Session has exipred, invalidate it */
                self::invalidateSessionCookie();
                self::invalidateSessionDB(session_id());
                return false;
            }

            /* The session is valid, Load all of the data into session, generate a new sid and update it in the database */
            $data = json_decode($row->data, true);
            foreach ($data as $key => $value)
            {
                $_SESSION[$key] = $value;
            }

            /* Add the necessary data to the class */
            session_regenerate_id(true);
            $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];

            /* update the session id to the database */
            $args = array("::usid" => $row->usid, "::sid" => session_id());
            return $db->query("UPDATE " . SystemTables::DB_TBL_USER_SESSION . " SET sid = '::sid' WHERE usid='::usid'", $args);
        }

        /**
         * Logout the user and destroy the session 
         */
        public static function logoutUser()
        {
            /* Invalidate the database session */
            self::invalidateSessionDB(session_id());

            /* Destroy the session variables */
            unset($_SESSION['uid']);
            unset($_SESSION['logged_in']);
            unset($_SESSION['logged_in_email']);
            unset($_SESSION['ipaddress']);
            unset($_SESSION['status']);

            /* Destroy the PHP Session */
            self::destroy();
        }

        /**
         * Invalidate a session from the database
         * 
         * @param $session_id The id of the session to invalidate
         */
        public static function invalidateSessionDB($session_id)
        {
            $db = Codeli::getInstance()->getDB();
            /* Set the session's status to 0 in the database */
            $db->query("UPDATE " . SystemTables::DB_TBL_USER_SESSION . " SET status = '0' WHERE sid='::sid'", array("::sid" => $session_id));
        }

        /**
         * Invalidate the current session cookie
         */
        public static function invalidateSessionCookie()
        {
            setcookie("jsmartsid", "", time() - 3600);
        }

        /**
         * Checks whether a user is logged in
         * 
         * @return Boolean - Whether the user is logged in or not
         */
        public static function isLoggedIn()
        {
            return (isset($_SESSION['logged_in']) && ($_SESSION['logged_in'] === true));
        }

        /**
         * @return The uid of the logged in user
         */
        public static function loggedInUid()
        {
            return isset($_SESSION['uid']) ? $_SESSION['uid'] : false;
        }

    }
    
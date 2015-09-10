<?php

    /**
     * Manages the current REST Session Information 
     * 
     * @author Joshua Kissoon
     * @since 20121212
     * 
     * @updated 20150910
     */
    class SessionManager
    {

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

            /* Add the necessary data to the class */
            $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['status'] = 1;

            /* Now we create the necessary cookies for the user and save the session data */
            setcookie("codelisid", session_id(), time() + BaseConfig::USER_SESSION_LIFETIME, "/");

            /* Save the entire session data to the database */
            $args = array(
                "::uid" => $_SESSION['uid'],
                "::sid" => session_id(),
                "::ipaddress" => $_SESSION['ipaddress'],
                "::status" => $_SESSION['status'],
                "::data" => json_encode($_SESSION),
            );


            $db = Codeli::getInstance()->getDB();
            /* Delete user session */
            $db->query("DELETE FROM " . DatabaseTables::USER_SESSION . " WHERE uid=::uid", array("::uid" => $user->getId()));

            /* Save the session data to the database */
            $db->query("INSERT INTO " . SystemTables::USER_SESSION . " (uid, sid, ipaddress, status, data) VALUES('::uid', '::sid', '::ipaddress', '::status', '::data')", $args);
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

            /* Invalidate the cookie */
            setcookie("codelisid", session_id(), time(), "/");

            /* Destroy the PHP Session */
            return self::destroy();
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
            $db->query("UPDATE " . SystemTables::USER_SESSION . " SET status = '0' WHERE sid='::sid'", array("::sid" => $session_id));
        }

    }
    
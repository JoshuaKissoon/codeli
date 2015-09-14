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
         * Initialize the session
         */
        public static function init()
        {
            session_start();
            SessionManager::loadDataFromToken();
        }

        /**
         * Load the data of the currently logged in user from the token
         */
        public static function loadDataFromToken()
        {
            $headers = getallheaders();
            $token = isset($headers['accessToken']) ? $headers['accessToken'] : "";
            $_SESSION["user_session"] = UserSession::loadSessionFromToken($token);
        }

        public static function loggedInUid()
        {
            if (!$_SESSION['user_session'])
            {
                return false;
            }

            return $_SESSION['user_session']->getUserId();
        }

        /**
         * Creates a new session and logs in a user
         * 
         * @param User The user to log in
         * 
         * @return {String} The token
         */
        public static function loginUser($uid)
        {
            session_regenerate_id(true);

            $us = new UserSession();
            $us->setUserId($uid);
            $us->setIpAddress($_SERVER['REMOTE_ADDR']);
            $us->setToken(session_id());
            $us->insert();
            return session_id();
        }

        /**
         * Invalidate all current sessions for the given user
         * 
         * @param {Integer} $uid The id of the user to invalidate sessions for
         * 
         * @author Joshua Kissoon
         * @since 20150910
         */
        public static function invalidateUserSessions($uid)
        {
            $db = Codeli::getInstance()->getDB();
            $db->query("UDPATE " . DatabaseTables::USER_SESSION . " SET ussid=2 WHERE uid=::uid AND ussid=1", array("::uid" => $uid));
        }

        /**
         * Logout the user and destroy the session 
         */
        public static function logoutUser($usid)
        {
            $db = Codeli::getInstance()->getDB();
            return $db->query("UDPATE " . DatabaseTables::USER_SESSION . " SET ussid=2 WHERE ussid=1 AND usid=::usid", array("::usid" => $usid));
        }

    }
    
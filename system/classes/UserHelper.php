<?php

    /**
     * Helper class for User Objects
     *
     * @author Joshua Kissoon
     */
    class UserHelper
    {

        /**
         * Checks if an email address is in use 
         */
        public static function isEmailInUse($email)
        {
            if (!valid($email))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            $res = $db->query("SELECT email FROM " . SystemTables::DB_TBL_USER . " WHERE email='::email'", array("::email" => $email));
            $temp = $db->fetchObject($res);
            return (isset($temp->email) && valid($temp->email)) ? true : false;
        }

        /**
         * Hashes the user's password using a salt
         * 
         * @return String - The hashed password
         */
        public static function hashPassword($password)
        {
            $salted = md5($password . BaseConfig::PASSWORD_SALT);
            return sha1($salted);
        }

    }
    
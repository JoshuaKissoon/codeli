<?php

    /**
     * Helper class for User Objects
     *
     * @author Joshua Kissoon
     */
    class UserHelper
    {

        private $user;
        private $roles = null;

        public function __construct($user)
        {
            $this->user = $user;
        }

        /**
         * @return Array - The roles this user have
         */
        public function getRoles()
        {
            if (null == $this->roles)
            {
                $this->loadRoles();
            }
            return $this->roles;
        }

        /**
         * Loads the roles that a user have
         * 
         * @return Array - The set of user roles
         */
        public function loadRoles()
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT ur.rid, r.* FROM " . SystemTables::USER_ROLE . " ur LEFT JOIN role r ON (r.rid = ur.rid) WHERE uid='$this->uid'";
            $roles = $db->query($sql);
            while ($row = $db->fetchObject($roles))
            {
                $role = new Role();
                $role->loadFromMap($row);
                $this->roles[$row->rid] = $role;
            }

            $this->isRolesLoaded = true;

            return $this->roles;
        }

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

            $res = $db->query("SELECT email FROM " . SystemTables::USER . " WHERE email='::email'", array("::email" => $email));
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
    
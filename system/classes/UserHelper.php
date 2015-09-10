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
        private $permissions = null;

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

            $sql = "SELECT ur.rid, r.* FROM " . SystemTables::USER_ROLE
                    . " ur LEFT JOIN role r ON (r.rid = ur.rid) WHERE uid=':uid'";
            $roles = $db->query($sql, array(":uid" => $this->user->getId()));
            while ($row = $db->fetchObject($roles))
            {
                $role = new Role();
                $role->loadFromMap($row);
                $this->roles[$row->rid] = $role;
            }

            return $this->roles;
        }

        /**
         * Load the permissions for this user from the database
         */
        public function loadPermissions()
        {
            if (count($this->getRoles()) < 1)
            {
                $this->permissions = array();
                return;
            }

            $db = Codeli::getInstance()->getDB();

            $rids = implode(", ", array_keys($this->getRoles()));
            $rs = $db->query("SELECT permission FROM " . SystemTables::ROLE_PERMISSION . " WHERE rid IN ($rids)");

            while ($perm = $db->fetchObject($rs))
            {
                $this->permissions[$perm->permission] = $perm->permission;
            }
        }

        /**
         * Check if the user has the specified permission
         * 
         * @param $permission The id of the permission to check if the user have
         * 
         * @return Boolean - Whether the user has the permission
         */
        public function hasPermission($permission)
        {
            if ($this->user->getId() == 1)
            {
                return true;
            }
            if (!valid($permission))
            {
                return false;
            }

            if (null == $this->permissions)
            {
                $this->loadPermissions();
            }

            return (key_exists($permission, $this->permissions)) ? true : false;
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
    
<?php

    /**
     * Class that contains core user functionality
     * 
     * @author Joshua Kissoon
     * @since 20121227
     * @updated 20150108
     */
    class JUser implements User
    {

        private $uid;
        private $userId;
        private $password;
        private $firstName;
        private $lastName;
        private $otherName;
        private $usid = 1;      // Status ID
        private $status;
        private $email;

        /**
         * User Roles Management
         */
        private $isRolesLoaded = false;
        private $roles = array();

        /* Some constants of what data is loaded */
        private $isPermissionsLoaded = false;
        private $permissions = array();

        /**
         * Error handlers 
         */
        public static $ERROR_INCOMPLETE_DATA = 00001;

        /**
         * Constructor method for the user class, loads the user
         * 
         * @param $uid The id of the user to load
         * 
         * @return Boolean - Whether the load was successful or not
         */
        public function __construct($uid = null)
        {
            if (null == $uid)
            {
                return false;
            }
            $this->uid = $uid;
            return $this->load();
        }

        public function getId()
        {
            return $this->uid;
        }

        public function setUserId($userId)
        {
            $this->userId = $userId;
        }

        public function setPassword($password)
        {
            $this->password = UserHelper::hashPassword($password);
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setFirstName($fname)
        {
            $this->firstName = $fname;
        }

        public function setLastName($lname)
        {
            $this->lastName = $lname;
        }

        public function setOtherName($oname)
        {
            $this->otherName = $oname;
        }

        public function getStatusId()
        {
            return $this->usid;
        }

        public function setStatusId($usid)
        {
            $this->usid = $usid;
        }

        /**
         * Checks if this is a user of the system
         * 
         * @param $uid The user of the user to check for
         * 
         * @return Boolean Whether this is a system user or not
         */
        public static function isExistent($uid)
        {
            if (!valid($uid))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            $args = array("::uid" => $uid);
            $sql = "SELECT uid FROM " . SystemTables::USER . " WHERE uid='::uid'";
            $res = $db->query($sql, $args);
            $user = $db->fetchObject($res);

            return (isset($user->uid) && valid($user->uid));
        }

        /**
         * Method that loads the user data from the database
         * 
         * @param $uid The id of the user to load
         * 
         * @return Boolean - Whether the load was successful or not
         */
        public function load()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(":uid" => $this->uid);
            $sql = "SELECT * FROM " . SystemTables::USER_V . " WHERE uid=':uid' LIMIT 1";
            $rs = $db->query($sql, $args);

            if ($db->resultNumRows($rs) != 1)
            {
                return false;
            }

            $data = $db->fetchObject($rs);
            return $this->loadFromMap($data);
        }

        /**
         * Load the user data from a given map
         * 
         * @param Map<String, String> $data The input data map in the form of an object
         */
        public function loadFromMap($data)
        {
            $this->uid = $data->uid;
            $this->userId = $data->userid;
            $this->email = $data->email;
            $this->usid = $data->usid;
            $this->status = $data->status;
            $this->firstName = $data->firstName;
            $this->lastName = $data->lastName;
            $this->otherName = $data->otherName;
        }

        /**
         * Check if this password given here is that of the user
         */
        public function isUserPassword($password)
        {
            return ($this->password == $this->hashPassword($password)) ? true : false;
        }

        /**
         * @todo
         */
        public function hasMandatoryData()
        {
            
        }

        /**
         * Save the data of this user to the database, if it's a new user, then create this new user
         */
        public function save()
        {
            if (isset($this->uid) && self::isExistent($this->uid))
            {
                return $this->update();
            }
            else
            {
                return $this->insert();
            }
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                ":userid" => $this->userId,
                ":firstName" => isset($this->firstName) ? $this->firstName : "",
                ":email" => isset($this->email) ? $this->email : "",
                ":lastName" => isset($this->lastName) ? $this->lastName : "",
                ":otherName" => isset($this->otherName) ? $this->otherName : "",
                ":password" => $this->password,
                ":usid" => $this->usid,
            );

            $sql = "INSERT INTO " . SystemTables::USER .
                    " (password, userid, email, firstName, lastName, otherName, usid)
                VALUES(':password', ':userid', ':email', ':firstName', ':lastName', ':otherName', ':usid')";

            $res = $db->query($sql, $args);

            if (!$res)
            {
                return false;
            }

            $this->uid = $db->lastInsertId();
            $this->saveRoles();
            return true;
        }

        public function update()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                ":userid" => $this->userId,
                ":firstName" => isset($this->firstName) ? $this->firstName : "",
                ":email" => isset($this->email) ? $this->email : "",
                ":lastName" => isset($this->lastName) ? $this->lastName : "",
                ":otherName" => isset($this->otherName) ? $this->otherName : "",
                ":password" => $this->password,
                ":usid" => $this->usid,
                ":uid" => $this->uid,
            );

            $sql = "UPDATE " . SystemTables::USER .
                    " SET password=':password', userid=':userid', email=':email', firstName=':firstName', 
                        lastName=':lastName', otherName=':otherName', usid=':usid' WHERE uid=:uid LIMIT 1";

            $res = $db->query($sql, $args);

            if (!$res)
            {
                return false;
            }

            return $this->saveRoles();
        }

        /**
         * Check if the email and password is valid
         * 
         * @return Boolean - whether the user credentials is valid
         */
        public function authenticate()
        {
            $db = Codeli::getInstance()->getDB();

            $args = array(
                ":userid" => $this->userId,
                "::password" => $this->password
            );
            $sql = "SELECT uid FROM " . SystemTables::USER .
                    " WHERE userid=':userid' and password='::password' LIMIT 1";

            $result = $db->fetchObject($db->query($sql, $args));

            if (isset($result->uid) && valid($result->uid))
            {
                $this->uid = $result->uid;
                return true;
            }
            return false;
        }

        /**
         * Deletes a user from the system
         * 
         * @param $uid The user ID of the user to delete
         * 
         * @return Boolean - Whether the user was deleted or not
         */
        public static function delete($uid)
        {
            if (!self::isExistent($uid))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();
            return $db->query("DELETE FROM " . SystemTables::USER . " WHERE uid='::uid'", array("::uid" => $uid));
        }

        /**
         * Adds a new role to a user
         * 
         * @param $rid the id of the role to add
         * 
         * @return Boolean - Whether the operation was successful
         */
        public function addRole($rid)
        {
            $db = Codeli::getInstance()->getDB();

            $res = $db->query("SELECT * FROM " . SystemTables::ROLE . " WHERE rid='::rid'", array('::rid' => $rid));

            if ($db->resultNumRows($res) != 1)
            {
                return false;
            }

            $this->roles[$rid] = new Role($rid);
            return true;
        }

        /**
         * Saves this user's roles to the Database
         * 
         * @return Boolean - Whether the operation was successful
         */
        public function saveRoles()
        {
            if (!self::isExistent($this->uid))
            {
                return false;
            }

            $db = Codeli::getInstance()->getDB();

            /* Remove all the roles this user had */
            $db->query("DELETE FROM " . SystemTables::USER_ROLE . " WHERE uid='$this->uid'");

            foreach ($this->roles as $rid => $role)
            {
                $sql = "INSERT INTO " . SystemTables::USER_ROLE . " (uid, rid) VALUES ('::uid', '::rid')";
                $args = array('::rid' => $rid, '::uid' => $this->uid);
                $db->query($sql, $args);
            }

            return true;
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
         * @return Array - The roles this user have
         */
        public function getRoles()
        {
            if (!$this->isRolesLoaded)
            {
                $this->loadRoles();
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
            if ($this->uid == 1)
            {
                return true;
            }
            if (!valid($permission))
            {
                return false;
            }

            if (!$this->isPermissionsLoaded)
            {
                $this->loadPermissions();
            }

            return (key_exists($permission, $this->permissions)) ? true : false;
        }

        /**
         * Method that returns an exposed version of the class's data
         */
        public function expose()
        {
            $object = get_object_vars($this);
            $object['roles'] = array();

            foreach ($this->roles as $role)
            {
                $object['roles'][$role->getId()] = $role->expose();
            }

            return $object;
        }

    }
    
<?php

    /**
     * Class that takes care of managing a user session
     *
     * @author Joshua Kissoon
     * @since 20150910
     */
    class UserSession implements DatabaseObject
    {

        /** Class attributes */
        private $usid;
        private $uid;
        private $token;
        private $ipAddress;
        private $ussid = 1;
        private $data = array();
        private $createdTimestamp;

        public function __construct($usid = null)
        {
            if (null == $usid)
            {
                return;
            }

            $this->usid = $usid;
            $this->load();
        }

        /**
         * Loads a user session from the given token 
         * 
         * @param String $token The token to load the session from 
         * 
         * @return UserSession 
         */
        public static function loadSessionFromToken($token)
        {
            if ("" == trim($token))
            {
                return false;
            }
            
            $db = Codeli::getInstance()->getDB();

            $args = array("::token" => $token);
            $sql = "SELECT * FROM " . SystemTables::USER_SESSION . " WHERE token='::token' LIMIT 1";
            $rs = $db->query($sql, $args);

            if ($db->resultNumRows($rs) != 1)
            {
                return false;
            }

            $us = new UserSession();
            $us->loadFromMap($db->fetchObject($rs));
            return $us;
        }

        public function getId()
        {
            return $this->usid;
        }

        public function setUserId($uid)
        {
            $this->uid = $uid;
        }

        public function getUserId()
        {
            return $this->uid;
        }

        public function setToken($token)
        {
            $this->token = $token;
        }

        public function setIpAddress($ip)
        {
            $this->ipAddress = $ip;
        }

        public function setStatusId($ussid)
        {
            $this->ussid = $ussid;
        }

        public function setData($data)
        {
            $this->data = $data;
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();
            $sql = "INSERT INTO " . SystemTables::USER_SESSION .
                    " (uid, token, ipaddress, ussid, data) "
                    . " VALUES('::uid', '::token', '::ipaddress', '::ussid', '::data')";
            $args = array(
                "::uid" => $this->uid,
                "::token" => $this->token,
                "::ipAddress" => $this->ipAddress,
                "::ussid" => $this->ussid,
                "::data" => json_encode($this->data),
            );

            $res = $db->query($sql, $args);
            if (!$res)
            {
                return false;
            }

            $this->usid = $db->lastInsertId();
            return true;
        }

        public function load()
        {
            
        }

        public function update()
        {
            $db = Codeli::getInstance()->getDB();
            $sql = "UPDATE " . SystemTables::USER_SESSION .
                    " SET ipaddress='::ipaddress', ussid='::ussid', data='::data' WHERE usid='::usid' LIMIT 1 ";
            $args = array(
                "::ipAddress" => $this->ipAddress,
                "::ussid" => $this->ussid,
                "::data" => json_encode($this->data),
                "::usid" => $this->usid,
            );

            return $db->query($sql, $args);
        }

        public static function delete($usid)
        {
            $db = Codeli::getInstance()->getDB();
            $sql = "DELETE FROM " . DatabaseTables::USER_SESSION . " WHERE usid=::usid LIMIT 1";
            $args = array("::usid" => $usid);
            return $db->query($sql, $args);
        }

        public static function isExistent($id)
        {
            
        }

        public function loadFromMap($data)
        {
            $this->usid = $data->usid;
            $this->uid = $data->uid;
            $this->token = $data->token;
            $this->ipAddress = $data->ipAddress;
            $this->ussid = $data->ussid;
            $this->data = $data->data;
            $this->createdTimestamp = $data->createdTimestamp;
        }

    }
    
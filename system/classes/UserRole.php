<?php

    /**
     * Representation of a UserRole
     *
     * @author Joshua Kissoon
     * @since 20150910
     */
    class UserRole implements DatabaseObject
    {

        private $urid;
        private $uid;
        private $rid;

        public function getId()
        {
            return $this->urid;
        }

        public function setUserId($uid)
        {
            $this->uid = $uid;
        }

        public function setRoleId($rid)
        {
            $this->rid = $rid;
        }

        public function insert()
        {
            $sql = "INSERT INTO " . SystemTables::USER_ROLE . " (uid, rid) VALUES ('::uid', '::rid') ON DUPLICATE KEY UPDATE rid=::rid";
            $args = array(
                '::rid' => $this->rid,
                '::uid' => $this->uid
            );
            $db = Codeli::getInstance()->getDB();

            $res = $db->query($sql, $args);

            if (!$res)
            {
                return false;
            }

            $this->urid = $db->lastInsertId();
            return true;
        }

        public function load()
        {
            
        }

        public function update()
        {
            
        }

        public static function delete($urid)
        {
            $db = Codeli::getInstance()->getDB();
            return $db->query("DELETE FROM " . SystemTables::USER_ROLE . " WHERE urid='::urid'", array("::urid" => $urid));
        }

        public static function isExistent($id)
        {
            
        }

    }
    
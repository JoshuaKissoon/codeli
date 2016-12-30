<?php

    /**
     * Representation of a user activity log
     *
     * @author Joshua Kissoon
     * @since 20150910
     */
    class ActivityLog implements DatabaseObject
    {

        private $alid;
        private $uid;
        private $objectType;  // Object Type
        private $oid; // Object Type
        private $alaid; // Action Id
        private $message;
        private $data;
        private $createdTimestamp;

        /**
         * Create a new Activity Log
         * 
         * @param {Integer} $uid            The User Id
         * @param {String}  $objectType     The activity log object type
         * @param {Integer} $oid            Activity Log Object Id
         * @param {Integer} $alaid          Action Id
         * @param {String}  $message        Message
         * @param {String}  $data           Any relating data
         */
        public function __construct($uid, $objectType, $oid, $alaid, $message = "", $data = "")
        {
            $this->uid = $uid;
            $this->objectType = $objectType;
            $this->oid = $oid;
            $this->alaid = $alaid;
            $this->message = $message;
            $this->data = $data;
        }

        public function getId()
        {
            return $this->alid;
        }

        public function setUid($val)
        {
            $this->uid = $val;
        }

        public function setObjectType($objectType)
        {
            $this->objectType = $objectType;
        }

        public function setObjectId($val)
        {
            $this->oid = $val;
        }

        public function setActionId($val)
        {
            $this->alaid = $val;
        }

        public function setMessage($val)
        {
            $this->message = $val;
        }

        public function setData($val)
        {
            $this->data = $val;
        }

        public function hasMandatoryData()
        {
            
        }

        public function insert()
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "INSERT INTO " . DatabaseTables::ADMIN_ACTIVITY_LOG .
                    " (uid, objectType, oid, alaid, message, data) "
                    . " VALUES ('::uid', '::objectType', '::oid', '::alaid', '::message', '::data')";
            $args = array(
                '::uid' => $this->uid,
                '::objectType' => $this->objectType,
                '::oid' => $this->oid,
                '::alaid' => $this->alaid,
                '::message' => $this->message,
                '::data' => json_encode($this->data)
            );

            $res = $db->query($sql, $args);

            if (!$res)
            {
                return false;
            }

            $this->alid = $db->lastInsertId();
            return true;
        }

        public function load()
        {
            
        }

        public function update()
        {
            
        }

        public static function delete($id)
        {
            
        }

        public static function isExistent($id)
        {
            
        }

        public function loadFromMap($data)
        {
            
        }

    }
    
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
        private $alot; // Object Type
        private $aloid; // Object Type
        private $alaid; // Action Id
        private $message;
        private $preObject;
        private $postObject;
        private $data;
        private $createdTimestamp;

        /**
         * Create a new Activity Log
         * 
         * @param {Integer} $uid            The User Id
         * @param {String}  $alot           The activity log object type
         * @param {Integer} $aloid          Activity Log Object Id
         * @param {Integer} $alaid          Action Id
         * @param {String}  $message        Message
         * @param {String}  $preObject      Object before action
         * @param {String}  $postObject     Object After the action
         * @param {String}  $data           Any relating data
         */
        public function __construct($uid, $alot, $aloid, $alaid, $message = "", $preObject = "", $postObject = "", $data = "")
        {
            $this->uid = $uid;
            $this->alot = $alot;
            $this->aloid = $aloid;
            $this->alaid = $alaid;
            $this->message = $message;
            $this->preObject = $preObject;
            $this->postObject = $postObject;
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

        public function setObjectType($alot)
        {
            $this->alot = $alot;
        }

        public function setObjectId($val)
        {
            $this->aloid = $val;
        }

        public function setActionId($val)
        {
            $this->alaid = $val;
        }

        public function setMessage($val)
        {
            $this->message = $val;
        }

        public function setPreObject($val)
        {
            $this->preObject = $val;
        }

        public function setPostObject($val)
        {
            $this->postObject = $val;
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

            $sql = "INSERT INTO " . DatabaseTables::ACTIVITY_LOG .
                    " (uid, alot, aloid, alaid, message, preObject, postObject, data) "
                    . " VALUES ('::uid', '::alot', '::aloid', '::alaid', '::message', '::preObject', '::postObject', '::data')";
            $args = array(
                '::uid' => $this->uid,
                '::alot' => $this->alot,
                '::aloid' => $this->aloid,
                '::alaid' => $this->alaid,
                '::message' => $this->message,
                '::preObject' => json_encode($this->preObject),
                '::postObject' => json_encode($this->postObject),
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

    }
    
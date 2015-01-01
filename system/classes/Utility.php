<?php

    /**
     * Class providing utility support methods for Sweia
     *
     * @author Joshua Kissoon
     * @since 20140616
     */
    class Utility
    {

        /**
         * Logs a message to the database
         * 
         * @param $type The type of message to log
         * @param $message
         */
        public static function log($type, $message)
        {  
            $db = Codeli::getInstance()->getDB();

            $res = $db->query("INSERT INTO system_log (type, message) VALUES (':type', ':message')", array(":type" => $type, ":message" => $message));
            return ($res) ? true : false;
        }

        /**
         * Set a variable in the site table that can be used later 
         * 
         * @param $vid The id by which to store the variable
         * @param $value The actual value to store
         */
        public static function variableSet($vid, $value)
        {
            $db = Codeli::getInstance()->getDB();

            $args = array("::vid" => $vid, "::value" => $value);
            $sql = "INSERT INTO variable (vid, value) VALUES ('::vid', '::value')
                ON DUPLICATE KEY UPDATE value='::value'";
            $res = $db->query($sql, $args);
            return $res;
        }

        /**
         * Retrieves a variable that was set earlier in the site variable table
         * 
         * @param $vid The id by of the variable to retrieve
         */
        public static function variableGet($vid)
        {
            $db = Codeli::getInstance()->getDB();

            $vid = $db->escapeString($vid);
            $res = $db->query("SELECT value FROM variable WHERE vid='::vid'", array("::vid" => $vid));
            $variable = $db->fetchObject($res);
            if (isset($variable->value))
            {
                return $variable->value;
            }
            else
            {
                return false;
            }
        }

        /**
         * @return The website's name
         */
        public static function getSiteName()
        {
            return self::variableGet("sitename");
        }

    }
    
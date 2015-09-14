<?php

    /**
     * Interface specifying the structure of any object that accesses the database
     * 
     * @author Joshua Kissoon
     * @since 20140526
     */
    interface DatabaseObject
    {

        /**
         * Check if an Object of this type is existent for a given id
         * 
         * @param Integer $id The Id of the object to use to check the object's existence
         */
        public static function isExistent($id);

        /**
         * @return Integer The id of this object from it's database table
         */
        public function getId();

        /**
         * Inserts a new row into the database with the data from this object
         * 
         * @return Boolean Whether the operation was successful
         */
        public function insert();

        /**
         * Assuming this object data is already in the database, 
         * update the current data.
         * 
         * @return Boolean Whether the operation was successful
         */
        public function update();

        /**
         * Assuming this object data is already in the database, 
         * delete the current data from the database.
         * 
         * @param Integer $id The id of this object to delete
         * 
         * @return Boolean Whether the operation was successful
         */
        public static function delete($id);

        /**
         * Load the data from the database for this object
         * 
         * @return Boolean Whether the operation was successful
         */
        public function load();

        /**
         * Loads the data to populate this object from an objected
         */
        public function loadFromMap($data);
    }
    
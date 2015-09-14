<?php

    /**
     * Representation of an API Object
     *
     * @author Joshua Kissoon
     * @since 20150914
     */
    interface RESTAPIObject
    {

        /**
         * Method that returns the object in Json Format to be posted to the API
         */
        public function toJson();
    }
    
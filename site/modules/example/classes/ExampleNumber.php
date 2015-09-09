<?php

    /**
     * A sample class to demonstrate the example module
     *
     * @author Joshua Kissoon
     * @since 20150909
     */
    class ExampleNumber
    {

        private $number;
        private $title;

        public function __construct($number, $title)
        {
            $this->number = $number;
            $this->title = $title;
        }

        /**
         * Method that returns an exposed version of the class's data
         */
        public function expose()
        {
            return get_object_vars($this);
        }

    }
    
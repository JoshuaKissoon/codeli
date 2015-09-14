<?php

    /**
     * A sample class to demonstrate the example module
     *
     * @author Joshua Kissoon
     * @since 20150909
     */
    class ExampleNumber implements RESTAPIObject
    {

        private $number;
        private $title;

        public function __construct($number, $title)
        {
            $this->number = $number;
            $this->title = $title;

            return $this;
        }
        
        public function getId()
        {
            return $this->number;
        }

        public function toJson()
        {
            return json_encode(get_object_vars($this));
        }

    }
    
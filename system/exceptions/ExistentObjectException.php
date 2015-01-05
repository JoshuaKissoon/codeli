<?php

    /**
     * An exception thrown when an object already exists and someone is trying to create another instance of it
     *
     * @author Joshua Kissoon
     * @since 20150105
     */
    class ExistentObjectException extends Exception
    {

        const EXCEPTION_CODE = 0003;

        public function __construct($message)
        {
            parent::__construct($message, ExistentObjectException::EXCEPTION_CODE, null);
        }

    }
    
<?php

    /**
     * An exception thrown when there is no route handler for a given route
     *
     * @author Joshua Kissoon
     * @since 20150112
     */
    class InvalidRouteException extends Exception
    {

        const EXCEPTION_CODE = 0004;

        public function __construct($message)
        {
            parent::__construct($message, InvalidRouteException::EXCEPTION_CODE, null);
        }

    }
    
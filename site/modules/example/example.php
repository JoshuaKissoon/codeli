<?php

    /**
     * Main handler class for the module
     */
    function example_number_add()
    {
        $number = new ExampleNumber(10, "Ten");
        $response = new APIResponse($number->expose(), "Number added", true);
        
        print $response->getJSONOutput();
    }

    function example_numbers_view()
    {
        
    }

    function example_number_view()
    {
        
    }

    function example_number_edit()
    {
        
    }

    function example_number_delete()
    {
        
    }
    
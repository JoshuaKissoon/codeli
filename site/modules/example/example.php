<?php

    require_once 'classes/ExampleNumber.php';

    /**
     * Main handler class for the module
     */
    function example_number_add()
    {
        $data = json_decode(file_get_contents("php://input"));

        $number = new ExampleNumber(10, "Ten");
        return new APIResponse((string) $number, "Number added", true);
    }

    function example_numbers_view()
    {
        $filters = json_decode(file_get_contents("php://input"));

        $numbers = array();
        $ten = new ExampleNumber(10, "Ten");
        $numbers[$ten->getId()] = $ten->toJson();
        $thirteen = new ExampleNumber(13, "Thirteen");
        $numbers[$thirteen->getId()] = $thirteen->toJson();
        $sixteen = new ExampleNumber(16, "Sixteen");
        $numbers[$sixteen->getId()] = $sixteen->toJson();
        $nineteen = new ExampleNumber(19, "Nineteen");
        $numbers[$nineteen->getId()] = $nineteen->toJson();

        return new APIResponse($numbers, "View all numbers", true);
    }

    function example_number_view()
    {
        $url = Codeli::getInstance()->getURL();
        $numberId = $url[2];    // Get the ID from the URL  eg: example/numbers/10 - The id of the number is in placeholder 2 in the URL while "example" is in 0

        /* Retrieve the number from the database if needed using the ID */
        $number = new ExampleNumber($numberId, "Number Name");
        return new APIResponse((string) $number, "Number Gotten", true);
    }

    function example_number_edit()
    {
        
    }

    function example_number_delete()
    {
        
    }
    
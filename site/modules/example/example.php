<?php

    require_once 'classes/ExampleNumber.php';

    /**
     * Main handler class for the module
     */
    function example_number_add()
    {
        $data = json_decode(file_get_contents("php://input"));

        $number = new ExampleNumber(10, "Ten");
        $response = new APIResponse((string) $number, "Number added", true);

        $response->output();
    }

    function example_numbers_view()
    {
        $filters = json_decode(file_get_contents("php://input"));

        $numbers = array(
            (string) new ExampleNumber(10, "Ten"),
            (string) new ExampleNumber(13, "Thirteen"),
            (string) new ExampleNumber(16, "Sixteen"),
            (string) new ExampleNumber(19, "Nineteen"),
        );

        $response = new APIResponse($numbers, "View all numbers", true);

        $response->output();
    }

    function example_number_view()
    {
        $url = Codeli::getInstance()->getURL();
        $numberId = $url[2];    // Get the ID from the URL  eg: example/numbers/10 - The id of the number is in placeholder 2 in the URL while "example" is in 0

        /* Retrieve the number from the database if needed using the ID */
        $number = new ExampleNumber($numberId, "Number Name");
        $response = new APIResponse((string) $number, "Number Gotten", true);

        $response->output();
    }

    function example_number_edit()
    {
        
    }

    function example_number_delete()
    {
        
    }
    
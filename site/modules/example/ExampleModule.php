<?php

    /**
     * Description of ExampleModule
     *
     * @author Joshua
     */
    class ExampleModule implements Module
    {

        private $name = "Example Module";
        private $description = "A Simple module to demonstrate how to develop modules";

        public function bootup()
        {
            
        }

        public function getTitle()
        {
            return $this->name;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function getDependencies()
        {
            return array();
        }

        /**
         * Try to always prefix permissions with the name of the module
         */
        public function getPermissions()
        {
            $permissions = array();

            $permissions[] = new PermissionInfo("example_number_view", "Example: View");
            $permissions[] = new PermissionInfo("example_number_add", "Example: Add");
            $permissions[] = new PermissionInfo("example_number_edit", "Example: Edit");
            $permissions[] = new PermissionInfo("example_number_delete", "Example: Delete");

            return $permissions;
        }

        /**
         * The callback function for these routes can be found in {example.php}
         */
        public function getRoutes()
        {

            $routes = array();

            /* Use PUT to add a new number */
            $routes[] = new RouteInfo("example/numbers", "example_number_add", "example_number_add", HTTP::METHOD_PUT);

            /* We use post for this callback as to allow filtering information to be sent */
            $routes[] = new RouteInfo("example/numbers", "example_numbers_view", "", HTTP::METHOD_POST);

            /* Use GET to get a single number, leave permission blank if anyone can view numbers */
            $routes[] = new RouteInfo("example/numbers/%", "example_number_view", "", HTTP::METHOD_GET);

            /* Use POST to update a single number */
            $routes[] = new RouteInfo("example/numbers/%", "example_number_edit", "example_number_edit", HTTP::METHOD_POST);

            /* Use DELETE to delete a number */
            $routes[] = new RouteInfo("example/numbers/%", "example_number_delete", "example_number_delete", HTTP::METHOD_DELETE);

            return $routes;
        }

    }
    
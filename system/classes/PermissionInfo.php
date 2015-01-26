<?php

    /**
     * A class that contains basic information of a permission
     *
     * @author Joshua Kissoon
     * @since 20150113
     */
    class PermissionInfo
    {

        private $permission;
        private $title; 
        private $description;

        public function __construct($permission, $title, $description = "")
        {
            $this->permission = $permission;
            $this->title = $title;
            $this->description = $description;
        }

        public function getPermission()
        {
            return $this->permission;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getDescription()
        {
            return $this->description;
        }

    }
    
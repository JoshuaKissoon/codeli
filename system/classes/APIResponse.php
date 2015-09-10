<?php

    /**
     * A class that contains an API response
     *
     * @author Joshua Kissoon
     * @since 20150107
     */
    class APIResponse
    {

        const STATUS_CODE_SUCCESS = 200;
        const STATUS_CODE_ERROR = 200;
        const STATUS_CODE_INVALID_URL = 404;
        const STATUS_SYSTEM_ERROR = 500;

        private $status;
        private $success;
        private $message;
        private $data;

        public function __construct($data = "", $message = "", $success = true, $status = APIResponse::STATUS_CODE_SUCCESS)
        {
            $this->status = $status;
            $this->success = $success;
            $this->message = $message;
            $this->data = $data;
        }

        public function setStatus($status)
        {
            $this->status = $status;
        }

        public function setSuccess($success)
        {
            $this->success = $success;
        }

        public function setMessage($message)
        {
            $this->message = $message;
        }

        public function setData($data)
        {
            $this->data = $data;
        }

        public function getJSONOutput()
        {
            $result = array(
                'status' => $this->status,
                'message' => $this->message,
                'data' => $this->data,
                'success' => $this->success
            );

            return json_encode($result);
        }

        public function output()
        {
            http_response_code($this->status);
            print $this->getJSONOutput();
        }

    }
    
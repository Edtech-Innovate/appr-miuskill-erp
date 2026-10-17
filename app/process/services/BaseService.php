<?php

require_once __DIR__ . '/../Helper.php';
require_once __DIR__ . '/../Logger.php';

class BaseService
{
    protected $db;
    // protected $conn;
    protected $logger;

    public function __construct($db, $logger = null)
    {
        $this->db = $db;
        // $this->conn = $conn;

        // If logger not passed, create new one (fallback)
        $this->logger = $logger ?: new Logger();
    }

    /**
     * SUCCESS Response Format
     */
    protected function success($data = [], $message = "OK", $statusCode = 200)
    {
        return api_response(
            true,
            $statusCode,
            "SUCCESS",
            $message,
            $data
        );
    }

    /**
     * ERROR Response Format
     */
    protected function error($message, $code = "ERROR", $httpCode = 400)
    {
        return api_response(
            false,
            $httpCode,
            $code,
            $message
        );
    }

    /**
     * Secure Input Filter
     */
    protected function filter($input)
    {
        return filterInput($input);
    }
}

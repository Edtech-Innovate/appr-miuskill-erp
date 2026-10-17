<?php

class Logger
{
    private $file;

    public function __construct($filename = "erp_api.log")
    {
        $this->file = __DIR__ . "/logs/" . $filename;

        if (!file_exists(__DIR__ . "/logs/")) {
            mkdir(__DIR__ . "/logs/", 0777, true);
        }
    }

    private function write($level, $message, $data = [])
    {
        $log = date("Y-m-d H:i:s") . " | {$level} | {$message} | " . json_encode($data) . "\n";
        file_put_contents($this->file, $log, FILE_APPEND);
    }

    public function info($msg, $data = []) { $this->write("INFO", $msg, $data); }
    public function error($msg, $data = []) { $this->write("ERROR", $msg, $data); }
}

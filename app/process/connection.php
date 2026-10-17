<?php
require_once "Helper.php";
require "../../includes/db-config.php";

try {
     $host=$hostname;
$dbname=$database;
$user=$username;
$pass=$password;
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

} catch (PDOException $e) {

    echo json_encode(api_response(
        false,
        500,
        "DB_CONNECTION_FAILED",
        "Database connection failed",
        ["error" => $e->getMessage()]
    ));
    exit;
}

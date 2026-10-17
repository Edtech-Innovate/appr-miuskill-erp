<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once "Helper.php";
require_once "headers.php";
require_once "connection.php";
require_once "Logger.php";
require_once "RequestHandler.php";
// echo('asdsa');die;
// ----------- Filter All Inputs -----------
$_GET  = filterInput($_GET);
$_POST = filterInput($_POST);
$logger ="";
$method = $_GET["method"] ?? "";
$uniId  = $_GET["uni_id"] ?? "";
$getDataLimit=$_GET['limit']??"";
if(!empty($_GET['filter'])){
$filters= base64_decode($_GET['filter'])??"";}else{
    $filters = " ";
}
// echo('<pre>');print_r($filters);die;
$appLogger = new Logger();
// echo('<pre>');print_r($getDataLimit);die;
if($method===""&&$uniId===""&&$getDataLimit===""){
    // echo('sadsa');die;
    $appLogger->info("Default university list triggered", [
        "reason" => "UNIDATA_FLAG",
        "method" => $method,
        "uni_id" => $uniId,
        "get"    => $_GET,
        "post"   => $_POST,
        "getDataLimit"=>$getDataLimit,
        "filters"=>$filters,
        "ip"     => $_SERVER['REMOTE_ADDR'] ?? null
    ]);

    $sql = "SELECT Universities.*
            FROM Universities
            ORDER BY ID ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        api_response(
            true,
            200,
            "UNIVERSITY_LIST",
            "Universities fetched successfully",
            $universities
        ),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}


if ($method === "") {
    echo json_encode(api_response(
        false,
        400,
        "METHOD_REQUIRED",
        "Please provide API method"
    ));
    exit;
}

$handler  = new RequestHandler($db);

try {
    $response = $handler->handle($method, $uniId,$getDataLimit,$filters);

    // echo json_encode(api_response(
    //     true,
    //     200,
    //     "SUCCESS",
    //     "Request processed successfully",
    //     $response
    // ));
//     echo json_encode(api_response(
//     true,
//     200,
//     "SUCCESS",
//     "Request processed successfully",
//     $response
// ));
// print_r($response);
// die;
echo json_encode(api_response(
    true,
    200,
    "SUCCESS",
    "Request processed successfully",
    $response
), JSON_INVALID_UTF8_SUBSTITUTE);
    exit;

} catch (Exception $e) {

    echo json_encode(api_response(
        false,
        500,
        "SERVER_ERROR",
        $e->getMessage()
    ));
    exit;
}

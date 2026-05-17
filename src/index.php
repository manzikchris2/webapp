<?php

declare(strict_types=1);





header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);




include "routes.php";

route($_SERVER['REQUEST_METHOD'], $path);


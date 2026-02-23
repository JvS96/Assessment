<?php
require_once __DIR__ . '/../bootstrap.php';

use Controllers\IssueApiController;
use Core\Response;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$controller = new IssueApiController();
Response::json($controller->store($data));
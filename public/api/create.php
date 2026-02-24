<?php
require_once __DIR__ . '/../bootstrap.php';

use Controllers\IssueApiController;

header('Content-Type: application/json');

$controller = new IssueApiController();
$controller->store();
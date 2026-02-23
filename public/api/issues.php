<?php

require_once __DIR__ . '/../bootstrap.php';

use Controllers\IssueApiController;

$controller = new IssueApiController();
$controller->index();
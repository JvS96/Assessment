<?php
require_once __DIR__ . '/bootstrap.php';

use Controllers\AuthController;

$auth = new AuthController();
$auth->callback();
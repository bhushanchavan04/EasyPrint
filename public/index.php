<?php

/**
 * EasyPrint SaaS - Front Controller
 * Modern Clean PHP 8.2+ Architecture
 */

// Define root path
define('EASYPRINT_ROOT', dirname(__DIR__));

// Bootstrap application
$app = require_once EASYPRINT_ROOT . '/app/bootstrap.php';

// Dispatch request
$app->run();


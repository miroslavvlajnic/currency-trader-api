<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';
$router = require_once __DIR__ . '/../app/routes.php';
$router->run($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

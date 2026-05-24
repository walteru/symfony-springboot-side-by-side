<?php

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test';
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = '0';
$_SERVER['APP_SECRET'] = $_ENV['APP_SECRET'] = 'demo-not-secret';
putenv('APP_ENV=test');
putenv('APP_DEBUG=0');
putenv('APP_SECRET=demo-not-secret');

require __DIR__.'/../vendor/autoload.php';

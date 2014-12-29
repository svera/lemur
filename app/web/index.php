<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';
if (getenv('LEMUR_ENV') == 'test') {
    return $app;
}
$app->run();

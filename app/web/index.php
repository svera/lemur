<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';
if (getenv('LEMUR_ENV') == 'test') {
    return $app;
}
$app->run();

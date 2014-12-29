<?php

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';
if (getenv('LEMUR_ENV') == 'test' || getenv('LEMUR_ENV') == 'travis') {
    return $app;
}
$app->run();

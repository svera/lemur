<?php

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/router.php';
if (getenv('LEMUR_ENV') == 'test') {
    return $app;
}
$app->run();

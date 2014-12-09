<?php

use Src\Models\Request;

$app->get('/', function() use ($app) {
    $demo = new Request();
    $demo->setName('Test');

    $app['doctrine.odm.mongodb.dm']->persist($demo);
    $app['doctrine.odm.mongodb.dm']->flush();
    $demos = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Models\\Request')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'demos' => $demos
    ));
});

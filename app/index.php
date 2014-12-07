<?php
require_once __DIR__.'/../vendor/autoload.php';

use Neutron\Silex\Provider\MongoDBODMServiceProvider;

$app = new Silex\Application();
$app->register(new MongoDBODMServiceProvider(), array(
    'doctrine.odm.mongodb.connection_options' => array(
        'database' => 'MONGODB_DB',
        'host'     => 'mongo',
        'options'  => array('fsync' => false)
    ),
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app['debug'] = true;

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig', array(
    ));
});

$app->run();

<?php
require_once __DIR__.'/../vendor/autoload.php';

use Neutron\Silex\Provider\MongoDBODMServiceProvider;
use App\Models\Request;

$app = new Silex\Application();
$app->register(new MongoDBODMServiceProvider(), array(
    'doctrine.odm.mongodb.connection_options' => array(
        'database' => 'MONGODB_DB',
        'host'     => 'mongo',
        'options'  => array('fsync' => false)
    ),
    'doctrine.odm.mongodb.documents' => array(
        0 => array(
            'type' => 'annotation',
            'path' => array(
                'app/models',
            ),
            'namespace' => 'App\Models'
        ),
    ),    
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app['debug'] = true;

$app->get('/', function() use ($app) {
    $demo = new Request();
    $demo->setName('Test');

    $app['doctrine.odm.mongodb.dm']->persist($demo);
    $app['doctrine.odm.mongodb.dm']->flush();
    $demos = $app['doctrine.odm.mongodb.dm']
    ->getRepository('App\\Models\\Request')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'demos' => $demos
    ));
});

$app->run();

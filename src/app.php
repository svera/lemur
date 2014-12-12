<?php
require_once __DIR__.'/../vendor/autoload.php';

use Neutron\Silex\Provider\MongoDBODMServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

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
                'src/models',
            ),
            'namespace' => 'Src\Models'
        ),
    ),
    'doctrine.odm.mongodb.proxies_dir' => '../var/cache/doctrine/odm/mongodb/Proxy',
    'doctrine.odm.mongodb.hydrators_dir' => '../var/cache/doctrine/odm/mongodb/Hydrator',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app['debug'] = true;

/**
 * This application middleware is triggered before the controller is executed
 * It checks if the request is a JSON and stores it as a PHP array if so
 */
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

return $app;

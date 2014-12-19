<?php

use Neutron\Silex\Provider\MongoDBODMServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

$app = new Silex\Application();

if (getenv('LEMUR_ENV') == 'devel') {
    require __DIR__.'/config/devel.php';
} elseif (getenv('LEMUR_ENV') == 'test') {
    require __DIR__.'/config/test.php';
} else {
    require __DIR__.'/config/prod.php';
}

$app->register(new MongoDBODMServiceProvider(), array(
    'doctrine.odm.mongodb.connection_options' => array(
        'database' => $app['config.db.name'],
        'host'     => 'mongo',
        'options'  => array('fsync' => false)
    ),
    'doctrine.odm.mongodb.documents' => array(
        0 => array(
            'type' => 'annotation',
            'path' => array(
                'src/models',
            ),
            'namespace' => 'Src\Entities'
        ),
    ),
    'doctrine.odm.mongodb.proxies_dir' => '/var/cache/doctrine/odm/mongodb/Proxy',
    'doctrine.odm.mongodb.hydrators_dir' => '/var/cache/doctrine/odm/mongodb/Hydrator',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

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

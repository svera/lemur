<?php

require_once __DIR__.'/../vendor/autoload.php';

use Neutron\Silex\Provider\MongoDBODMServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

$app = new Silex\Application();
$environment = getenv('LEMUR_ENV');

if ($environment == 'devel') {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    require __DIR__.'/config/devel.php';
} elseif ($environment == 'test') {
    require __DIR__.'/config/test.php';
} else {
    require __DIR__.'/config/prod.php';
}

if ($environment == 'prod' || $environment == 'devel') {
    require __DIR__.'/config/secrets.php';
} else {
    $app['config.secrets.github_client_id'] = '';
    $app['config.secrets.github_client_secret'] = '';
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
                'src/entities',
            ),
            'namespace' => 'Src\Entities'
        ),
    ),
    'doctrine.odm.mongodb.proxies_dir' => '/var/cache/doctrine/odm/mongodb/Proxy',
    'doctrine.odm.mongodb.hydrators_dir' => '/var/cache/doctrine/odm/mongodb/Hydrator',
));

/**
 * Sessions provider
 */
$app->register(new Silex\Provider\SessionServiceProvider());

/**
 * Twig views provider
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

/**
 * OAuth2
 */
$app['oauth2'] = new League\OAuth2\Client\Provider\Github(array(
    'clientId'     =>  $app['config.secrets.github_client_id'],
    'clientSecret' =>  $app['config.secrets.github_client_secret'],
    'scopes' => array('user:email', 'read:org'),
    'domain' => $app['config.auth.domain']
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

<?php

use Silex\WebTestCase;

class ControllersTest extends WebTestCase
{
    public function createApplication()
    {
        $app_env = 'test';
        $app = require __DIR__.'/../../web/index.php';
        //$app['debug'] = true;
        //$app['exception_handler']->disable();
        return $app;
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
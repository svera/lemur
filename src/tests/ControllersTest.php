<?php

use Silex\WebTestCase;

class ControllersTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../app.php';
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

         $this->assertTrue($client->getResponse()->isOk());
    }
}
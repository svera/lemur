<?php

class ControllersTest extends PHPUnit_Framework_TestCase
{
    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

         $this->assertTrue($client->getResponse()->isOk());
    }
}
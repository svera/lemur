<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllersGitlabTest extends WebTestCase
{
    public function createApplication()
    {
        putenv('LEMUR_ENV=test');
        return require __DIR__.'/../../web/index.php';
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testNonExistentUrl()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/nonexistent');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testNoPayloadOrWrongPayloadSent()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/gitlab/pullRequest',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            ''
        );
        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testNewAndUpdateGitlabMergeRequest()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/gitlab/pullRequest',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            file_get_contents(__DIR__.'/fixtures/gitlabNewMergeRequestPayload.json')
        );
        $this->assertEquals(
            Response::HTTP_CREATED,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCloseGitlabMergeRequest()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/gitlab/pullRequest',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            file_get_contents(__DIR__.'/fixtures/gitlabCloseMergeRequestPayload.json')
        );
        $this->assertTrue($client->getResponse()->isOk());
    }

}
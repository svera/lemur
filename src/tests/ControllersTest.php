<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllersTest extends WebTestCase
{
    public function createApplication()
    {
        $app_env = 'test';
        $app = require __DIR__.'/../../web/index.php';
        $app['config.db.name'] = 'test';
        return $app;
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

    public function testCreateNewGithubPullRequest()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/github/pullRequest',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            file_get_contents(__DIR__.'/fixtures/githubNewPullRequestPayload.json')
        );
        $this->assertEquals(
            Response::HTTP_CREATED,
            $client->getResponse()->getStatusCode()
        );
    }
}
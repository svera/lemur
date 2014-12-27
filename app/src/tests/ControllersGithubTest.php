<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllersGithubTest extends WebTestCase
{
    public function createApplication()
    {
        putenv('LEMUR_ENV=test');
        $app = require __DIR__.'/../../web/index.php';
        $app['session.test'] = true;
        return $app;
    }

    public function testInitialPage()
    {
        $this->app['session']->set('access_token', 'fakeToken');
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testNonExistentUrl()
    {
        $client = $this->createClient();
        $client->request('GET', '/nonexistent');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testNoPayloadOrWrongPayloadSent()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/github/pullRequest',
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

    public function testNewAndUpdateGithubPullRequest()
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
        $client->request(
            'POST',
            '/github/pullRequestComment',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            file_get_contents(__DIR__.'/fixtures/githubNewPullRequestCommentPayload.json')
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCloseGithubPullRequest()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/github/pullRequest',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            file_get_contents(__DIR__.'/fixtures/githubClosePullRequestPayload.json')
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

}
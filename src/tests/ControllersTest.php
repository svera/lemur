<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllersTest extends WebTestCase
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
        $this->assertTrue($client->getResponse()->isOk());
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
        $this->assertTrue($client->getResponse()->isOk());
    }

}
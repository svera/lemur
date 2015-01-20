<?php
namespace Tests;

require_once __DIR__.'/../../vendor/autoload.php';

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllersTest extends WebTestCase
{
    public function createApplication()
    {
        putenv('LEMUR_ENV=test');
        $app = require __DIR__.'/../../web/index.php';
        $app['session.test'] = true;
        //$app['doctrine.odm.mongodb.dm']->getDocumentCollection('Src\\Entities\\PullRequest')->remove([]);
        return $app;
    }

    public function testInitialPageLogged()
    {
        $this->app['session']->set('access_token', 'fakeToken');
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testInitialPageNotLogged()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider nonExistentUrlProvider
     */
    public function testNonExistentUrl($url, $method, $expected)
    {
        $client = $this->createClient();
        $client->request($method, $url);
        $this->assertEquals($expected, $client->getResponse()->getStatusCode());
    }

    public function nonExistentUrlProvider()
    {
        return [
                    [
                        '/non-existent',
                        'GET',
                        Response::HTTP_NOT_FOUND
                    ],
                    [
                        '/unexistent-platform/pull-request',
                        'POST',
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    ],
                    [
                        '/unexistent-platform/pull-request-comment',
                        'POST',
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    ]
               ];
    }

    /**
     * @dataProvider noPayloadOrWrongPayloadProvider
     */
    public function testNoPayloadOrWrongPayloadSent($url, $payload)
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $client->getResponse()->getStatusCode()
        );
    }

    public function noPayloadOrWrongPayloadProvider()
    {
        return [
                  ['/github/pull-request', ''],
                  ['/gitlab/pull-request', '']
               ];
    }

    /**
     * @dataProvider newAndUpdatePullRequestProvider
     */
    public function testNewAndUpdatePullRequest($url, $headers, $payload, $expected)
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            $url,
            [],
            [],
            $headers,
            $payload
        );
        $this->assertEquals($expected, $client->getResponse()->getStatusCode());
    }

    public function newAndUpdatePullRequestProvider()
    {
        return [
                    [
                        '/github/pull-request',
                        [
                            'CONTENT_TYPE' => 'application/json',
                        ],
                        file_get_contents(__DIR__.'/fixtures/githubNewPullRequestPayload.json'),
                        Response::HTTP_CREATED
                    ],
                    [
                        '/github/pull-request-comment',
                        [
                            'CONTENT_TYPE' => 'application/json',
                            'HTTP_X-GitHub-Event' => 'pull_request_review_comment'
                        ],
                        file_get_contents(__DIR__.'/fixtures/githubNewPullRequestReviewCommentPayload.json'),
                        Response::HTTP_OK
                    ],
                    [
                        '/github/pull-request-comment',
                        [
                            'CONTENT_TYPE' => 'application/json',
                            'HTTP_X-GitHub-Event' => 'issue_comment'
                        ],
                        file_get_contents(__DIR__.'/fixtures/githubNewIssueCommentPayload.json'),
                        Response::HTTP_OK
                    ],
                    [
                        '/github/pull-request-comment',
                        [
                            'CONTENT_TYPE' => 'application/json',
                            'HTTP_X-GitHub-Event' => 'pull_request_review_comment'
                        ],
                        file_get_contents(__DIR__.'/fixtures/githubCommentUnexistentPullRequestPayload.json'),
                        Response::HTTP_GONE
                    ],
                    [
                        '/gitlab/pull-request',
                        [
                            'CONTENT_TYPE' => 'application/json',
                        ],
                        file_get_contents(__DIR__.'/fixtures/gitlabNewMergeRequestPayload.json'),
                        Response::HTTP_CREATED
                    ],
                    [
                        '/gitlab/pull-request-comment',
                        [
                            'CONTENT_TYPE' => 'application/json',
                        ],
                        '',
                        Response::HTTP_BAD_REQUEST
                    ]
               ];
    }

    /**
     * @dataProvider closePullRequestProvider
     */
    public function testClosePullRequest($url, $payload, $expected)
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $this->assertEquals($expected, $client->getResponse()->getStatusCode());
    }

    public function closePullRequestProvider()
    {
        return [
                    [
                        '/github/pull-request',
                        file_get_contents(__DIR__.'/fixtures/githubClosePullRequestPayload.json'),
                        Response::HTTP_OK
                    ],
                    [
                        '/gitlab/pull-request',
                        file_get_contents(__DIR__.'/fixtures/gitlabCloseMergeRequestPayload.json'),
                        Response::HTTP_OK
                    ],
                    [
                        '/github/pull-request',
                        file_get_contents(__DIR__.'/fixtures/githubCloseUnenxistentPullRequestPayload.json'),
                        Response::HTTP_GONE
                    ],
                    [
                        '/gitlab/pull-request',
                        file_get_contents(__DIR__.'/fixtures/gitlabCloseUnenxistentMergeRequestPayload.json'),
                        Response::HTTP_GONE
                    ]
               ];
    }

    public function testRefreshUrlLogged()
    {
        $this->app['session']->set('access_token', 'fakeToken');
        $client = $this->createClient();
        $crawler = $client->request('GET', '/refresh');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testRefreshUrlNotLogged()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/refresh');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}

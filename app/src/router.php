<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Entities\PullRequest;
use Src\Platforms\PayloadFactory;
use Src\Controllers\PullRequestController;

$app->get('/', function(Request $httpRequest) use ($app) {
    if ($app['session']->get('access_token') != null) {
        return $app->redirect('/pull-requests');
    }
    return $app['twig']->render(
        'index.twig',
        [
            'loginPath' => $app['oauth2']->getAuthorizationUrl(),
            'warn' => $httpRequest->query->get('warn')
        ]
    );
});

$app->get('/logout', function() use ($app) {
    $app['session']->set('access_token', null);
    return $app->redirect('/');
});

$app->get('/pull-requests', function() use ($app) {
    if ($app['session']->get('access_token') == null) {
        return $app->redirect('/?warn=You+need+to+be+logged+in+to+access+this+page.');
    }
    return $app['twig']->render('main.twig', []);
});

$app->get('/auth/github/callback', function(Request $httpRequest) use ($app) {
    $token = $app['oauth2']->getAccessToken('authorization_code', [
        'code' => $httpRequest->query->get('code')
    ]);
    $app['session']->set('access_token', $token);
    return $app->redirect('/');
});

/**
 * Centralized entry point for all webhook events
 */
$app->post('/{vcsName}/event', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if (!$payload) {
        return new Response('Platform not supported', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $pullRequestController = new PullRequestController($app['doctrine.odm.mongodb.dm']);

    switch ($payload->getType()) {
        case 'pull-request-open':
            return $pullRequestController->open($payload);
        case 'pull-request-close':
            return $pullRequestController->close($payload);
        case 'pull-request-comment':
            return $pullRequestController->update($payload);
        default:
            return new Response('Payload error', Response::HTTP_BAD_REQUEST);
    }
});

$app->get('/refresh', function() use ($app) {
    if ($app['session']->get('access_token') == null) {
        return new Response('Forbiddden', Response::HTTP_FORBIDDEN);
    }
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Entities\\PullRequest')
    ->findBy(
        ['status' => 'open']
    );
    $response = [
            'number_pull_requests' => count($pullRequests),
            'html' => $app['twig']->render(
                'pullRequests.twig',
                ['pullRequests' => $pullRequests]
            )
    ];
    return $app->json($response, Response::HTTP_OK);
});

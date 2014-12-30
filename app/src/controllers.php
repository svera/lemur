<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Entities\PullRequest;
use Src\Platforms\PayloadFactory;

$app->get('/login', function() use ($app) {
    if ($app['session']->get('access_token') != null) {
        return $app->redirect('/');
    }
    return $app['twig']->render(
        'login.twig',
        ['loginPath' => $app['oauth2']->getAuthorizationUrl()]
    );
});

$app->get('/logout', function() use ($app) {
    $app['session']->set('access_token', null);
    return $app->redirect('/login');
});

$app->get('/', function() use ($app) {
    if ($app['session']->get('access_token') == null) {
        return $app->redirect('/login');
    }
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Entities\\PullRequest')
    ->findAll(
        ['status' => 'open']
    );
    return $app['twig']->render(
        'index.twig',
        [
            'pullRequests' => $pullRequests,
            'refreshTime'  => $app['config.refreshTime']
        ]
    );
});

$app->get('/auth/github/callback', function(Request $httpRequest) use ($app) {
    $token = $app['oauth2']->getAccessToken('authorization_code', [
        'code' => $httpRequest->query->get('code')
    ]);
    $app['session']->set('access_token', $token);
    return $app->redirect('/');
});

$app->post('/{vcsName}/pullRequest', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreatePullRequestPayload()) {
        $pullRequest = $payload->createPullRequest();
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Pull request created', Response::HTTP_CREATED);
    }

    if ($payload->isClosePullRequestPayload()) {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'id' => $payload->getPullRequestIdFromPayload(),
                    'vcs' => $payload::VCSNAME
                ]
            );
        if ($pullRequest) {
            $pullRequest = $payload->setClosed($pullRequest);
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Pull request closed', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }
    return new Response('Payload error', Response::HTTP_BAD_REQUEST);
});

$app->post('/{vcsName}/pullRequestComment', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreateCommentPayload()) {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'id' => $payload->getPullRequestIdFromPayload(),
                    'vcs' => $payload::VCSNAME
                ]
            );
        $pullRequest = $payload->updateComments($pullRequest);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Pull request updated', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }
    return new Response('Payload error', Response::HTTP_BAD_REQUEST);
});

$app->get('/refresh', function() use ($app) {
    if ($app['session']->get('access_token') == null) {
        return new Response('Forbiddden', Response::HTTP_FORBIDDEN);
    }
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Entities\\PullRequest')
    ->findAll(
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
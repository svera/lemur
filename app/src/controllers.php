<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Entities\PullRequest;
use Src\Platforms\PayloadFactory;

$app->get('/', function() use ($app) {
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Entities\\PullRequest')
    ->findAll(
        array(
            'status' => 'open'
        )
    );
    return $app['twig']->render('index.twig', array(
        'pullRequests' => $pullRequests,
        'refreshTime'  => $app['config.refreshTime']
    ));
});

$app->post('/{vcsName}/pullRequest', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreatePullRequestPayload()) {
        $pullRequest = $payload->createPullRequest();
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Pull request reated', 201);
    }

    if ($payload->isClosePullRequestPayload()) {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                array(
                    'id' => $payload->getPullRequestIdFromPayload(),
                    'vcs' => $payload::VCSNAME
                )
            );
        if ($pullRequest) {
            $pullRequest->setClosed($PullRequest);
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Pull request closed', 200);
        }
        return new Response('Pull request not found', 410);
    }
});

$app->post('/{vcsName}/pullRequestComment', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreateCommentPayload()) {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                array(
                    'id' => $payload->getPullRequestIdFromPayload(),
                    'vcs' => $payload::VCSNAME
                )
            );
        $pullRequest = $payload->updateComments($pullRequest);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Pull request updated', 200);
        }
        return new Response('Pull request not found', 410);
    }
});

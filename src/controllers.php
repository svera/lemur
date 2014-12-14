<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Models\PullRequest;
use Src\Platforms\PayloadFactory;

$app->get('/', function() use ($app) {
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Models\\PullRequest')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'pullRequests' => $pullRequests
    ));
});

$app->post('/{vcsName}/pullRequest', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreatePullRequestAction()) {
        $pullRequest = $payload->createPullRequest();
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Created', 201);
    }

    if ($payload->isClosePullRequestAction()) {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
            ->getRepository('Src\\Models\\PullRequest')
            ->findOneBy(
                array(
                    'id' => $payload->getPullRequestIdFromPayload(),
                    'vcs' => $payload::VCSNAME
                )
            );
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->remove($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Removed', 200);
        }
        return new Response('Not found', 410);
    }
});

$app->post('/{vcsName}/pullRequestComment', function(Request $httpRequest, $vcsName) use ($app) {
    $payload = PayloadFactory::create($vcsName, $httpRequest);
    if ($payload->isCreateCommentAction()) {
        $pullRequest = $payload->updateComments($pullRequest);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Updated', 200);
        }
        return new Response('Not found', 410);
    }
});

//return new Response('Not found', 404);

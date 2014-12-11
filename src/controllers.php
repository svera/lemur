<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Models\PullRequest;
use Src\Models\Github;

$app->get('/', function() use ($app) {
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Models\\PullRequest')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'pullRequests' => $pullRequests
    ));
});

$app->post('/{vcs}/pullRequest', function(Request $httpRequest) use ($app) {
    $vcs = new Github($httpRequest);
    if ($vcs->isCreatePullRequestAction()) {
        $pullRequest = $vcs->createPullRequest();
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Created', 201);
    }

    if ($vcs->isClosePullRequestAction()) {
        $pullRequest = $vcs->loadPullRequest();
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->remove($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Removed', 200);
        }
        return new Response('Not found', 410);
    }
});

$app->post('/{vcs}/pullRequestComment', function(Request $httpRequest) use ($app) {
    $vcs = new Github($httpRequest);
    if ($vcs->isCreateCommentAction()) {
        $pullRequest = $vcs->updateComments($pullRequest);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Updated', 200);
        }
        return new Response('Not found', 410);
    }
});

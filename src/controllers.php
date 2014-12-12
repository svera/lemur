<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Models\PullRequest;
use Src\Platforms\VcsFactory;

$app->get('/', function() use ($app) {
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Models\\PullRequest')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'pullRequests' => $pullRequests
    ));
});

$app->post('/{vcsName}/pullRequest', function(Request $httpRequest, $vcsName) use ($app) {
    $vcs = VcsFactory::create($vcsName, $httpRequest);
    if ($vcs->isCreatePullRequestAction()) {
        $pullRequest = $vcs->createPullRequest();
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Created', 201);
    }

    if ($vcs->isClosePullRequestAction()) {
        $pullRequest = $vcs->loadPullRequest($app['doctrine.odm.mongodb.dm']);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->remove($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Removed', 200);
        }
        return new Response('Not found', 410);
    }
});

$app->post('/{vcsName}/pullRequestComment', function(Request $httpRequest, $vcsName) use ($app) {
    $vcs = VcsFactory::create($vcsName, $httpRequest);
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

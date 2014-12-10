<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Models\PullRequest;

$app->get('/', function() use ($app) {
    $pullRequests = $app['doctrine.odm.mongodb.dm']
    ->getRepository('Src\\Models\\PullRequest')
    ->findAll();
    return $app['twig']->render('index.twig', array(
        'pullRequests' => $pullRequests
    ));
});

$app->post('/github/pullRequest', function(Request $request) use ($app) {
    $payload = json_decode($request->get('payload'), true);
    if ($payload['action'] == 'opened' || $payload['action'] == 'reopened') {
        $pullRequest = new PullRequest();
        $pullRequest->setId($payload['pull_request']['id']);
        $pullRequest->setName($payload['pull_request']['title']);
        $pullRequest->setCreatedBy($payload['pull_request']['user']['login']);
        $pullRequest->setCreatedAt($payload['pull_request']['created_at']);
        $pullRequest->setVcs('Github');
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Created', 201);
    }

    if ($payload['action'] == 'closed') {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
        ->getRepository('Src\\Models\\PullRequest')
        ->find($payload['pull_request']['id']);
        if ($pullRequest) {
            $app['doctrine.odm.mongodb.dm']->remove($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Removed', 200);
        }
        return new Response('Not found', 410);
    }
});

$app->post('/github/pullRequestComment', function(Request $request) use ($app) {
    $payload = json_decode($request->get('payload'), true);
    if ($payload['action'] == 'created') {
        $pullRequest = $app['doctrine.odm.mongodb.dm']
        ->getRepository('Src\\Models\\PullRequest')
        ->find($payload['pull_request']['id']);
        if ($pullRequest) {
            $pullRequest->setNumberComments($pullRequest->getNumberComments() + 1);
            if (strpos($payload['comment']['body'], '+1') !== false) {
                $pullRequest->setNumberApprovals($pullRequest->getNumberApprovals() + 1);
            }
            if (strpos($payload['comment']['body'], '-1') !== false) {
                $pullRequest->setNumberApprovals($pullRequest->getNumberDisapprovals() + 1);
            }
            $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
            $app['doctrine.odm.mongodb.dm']->flush();
            return new Response('Updated', 200);
        }
        return new Response('Not found', 410);
    }
});

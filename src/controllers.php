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

$app->post('/', function(Request $request) use ($app) {
    $payload = json_decode($request->get('payload'), true);
    if ($payload['action'] == 'opened') {
        $pullRequest = new PullRequest();
        $pullRequest->setName($payload['pull_request']['title']);
        $pullRequest->setCreatedBy($payload['pull_request']['user']['login']);
        $pullRequest->setCreatedAt($payload['pull_request']['created_at']);
        $pullRequest->setVcs('Github');
        $app['doctrine.odm.mongodb.dm']->persist($pullRequest);
        $app['doctrine.odm.mongodb.dm']->flush();
        return new Response('Thank you!', 200);
    }
});

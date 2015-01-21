<?php

namespace Src\Controllers;

use Symfony\Component\HttpFoundation\Response;

class PullRequestController
{
    private $odm;

    public function __construct($odm)
    {
        $this->odm = $odm;
    }

    public function open($payload)
    {
        $pullRequest = $this->odm
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'repositoryId' => $payload->getRepositoryIdFromPayload(),
                    'number' => $payload->getPullRequestNumberFromPayload(),
                    'vcs' => $payload::VCSNAME
                ]
            );

        if ($pullRequest) {
            $pullRequest->status = 'open';
        } else {
            $pullRequest = $payload->createPullRequest();
        }
        $this->odm->persist($pullRequest);
        $this->odm->flush();
        return new Response('Pull request created', Response::HTTP_CREATED);
    }

    public function close($payload)
    {
        $pullRequest = $this->odm
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'repositoryId' => $payload->getRepositoryIdFromPayload(),
                    'number' => $payload->getPullRequestNumberFromPayload(),
                    'vcs' => $payload::VCSNAME
                ]
            );

        if ($pullRequest) {
            $pullRequest = $payload->setClosed($pullRequest);
            $this->odm->persist($pullRequest);
            $this->odm->flush();
            return new Response('Pull request closed', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }

    public function update($payload)
    {
        $pullRequest = $this->odm
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'repositoryId' => $payload->getRepositoryIdFromPayload(),
                    'number' => $payload->getPullRequestNumberFromPayload(),
                    'vcs' => $payload::VCSNAME
                ]
            );

        if ($pullRequest) {
            $pullRequest = $payload->updateComments($pullRequest);
            $this->odm->persist($pullRequest);
            $this->odm->flush();
            return new Response('Pull request updated', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }
}

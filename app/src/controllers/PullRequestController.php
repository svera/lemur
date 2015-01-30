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
        $pullRequest = $this->getPullRequest($payload);

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
        $pullRequest = $this->getPullRequest($payload);

        if ($pullRequest) {
            $pullRequest->status = 'closed';
            $pullRequest->updatedAt = $payload->getEventDateTime();
            $this->odm->persist($pullRequest);
            $this->odm->flush();
            return new Response('Pull request closed', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }

    public function updateComments($payload)
    {
        $pullRequest = $this->getPullRequest($payload);

        if ($pullRequest) {
            if ($this->isApproval($payload->getComment())) {
                $pullRequest->numberApprovals++;
            } elseif ($this->isDisapproval($payload->getComment())) {
                $pullRequest->numberDisapprovals++;
            } else {
                $pullRequest->numberComments++;
            }
            $this->odm->persist($pullRequest);
            $this->odm->flush();
            return new Response('Pull request updated', Response::HTTP_OK);
        }
        return new Response('Pull request not found', Response::HTTP_GONE);
    }

    private function isApproval($comment)
    {
        return strpos($comment(), '+1') === 0 ||
                strpos($comment(), ':+1:') === 0;
    }

    private function isDisapproval($comment)
    {
        return strpos($comment(), '-1') === 0 ||
                strpos($comment(), ':-1:') === 0;
    }

    private function getPullRequest($payload)
    {
        return $this->odm
            ->getRepository('Src\\Entities\\PullRequest')
            ->findOneBy(
                [
                    'repositoryId' => $payload->getRepositoryId(),
                    'number' => $payload->getPullRequestNumber(),
                    'vcs' => $payload::VCSNAME
                ]
            );
    }
}

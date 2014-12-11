<?php

namespace Src\Models;

use Src\Models\PullRequest;
use Symfony\Component\HttpFoundation\Request;

class Gitlab implements Vcs
{
    private $payload;
    const VCSNAME = 'gitlab';

    public function __construct(Request $httpRequest)
    {
        $this->payload = json_decode($httpRequest->get('payload'), true);
    }

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->setId($this->payload['object_attributes']['id']);
        $pullRequest->setName($this->payload['object_attributes']['title']);
        $pullRequest->setCreatedBy($this->payload['user']['name']);
        $pullRequest->setCreatedAt($this->payload['object_attributes']['created_at']);
        $pullRequest->setVcs($this->VCSNAME);
        $pullRequest->setHtmlUrl($this->payload['object_attributes']['source']['http_url']);
        return $pullRequest;
    }

    public function isCreatePullRequestAction()
    {
        return $this->payload['object_attributes']['state'] == 'opened';
    }

    public function isClosePullRequestAction()
    {
        return $this->payload['object_attributes']['state'] == 'closed';
    }

    public function isCommentCreatedAction()
    {
        return false;
    }

    public function updateComments(PullRequest $pullRequest)
    {
        return false;
    }

    public function loadPullRequest($db)
    {
        $pullRequest = $db
            ->getRepository('Src\\Models\\PullRequest')
            ->findOneBy(
                array(
                    'id' => $this->payload['object_attributes']['id'],
                    'vcs' => $this->VCSNAME
                )
            );
        if ($pullRequest) {
            return $pullRequest;
        }
        return false;
    }
}

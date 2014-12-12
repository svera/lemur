<?php

namespace Src\Models;

use Src\Models\PullRequest;
use Symfony\Component\HttpFoundation\Request;

class Github extends Vcs implements VcsInterface
{
    const VCSNAME = 'github';

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->setId($this->payload['pull_request']['id']);
        $pullRequest->setName($this->payload['pull_request']['title']);
        $pullRequest->setCreatedBy($this->payload['pull_request']['user']['login']);
        $pullRequest->setCreatedAt($this->payload['pull_request']['created_at']);
        $pullRequest->setNumberComments(0);
        $pullRequest->setNumberApprovals(0);
        $pullRequest->setNumberDisapprovals(0);
        $pullRequest->setVcs(self::VCSNAME);
        $pullRequest->setHtmlUrl($this->payload['pull_request']['html_url']);
        return $pullRequest;
    }

    public function isCreatePullRequestAction()
    {
        return $this->payload['action'] == 'opened' || $this->payload['action'] == 'reopened';
    }

    public function isClosePullRequestAction()
    {
        return $this->payload['action'] == 'closed';
    }

    public function isCommentCreatedAction()
    {
        return $this->payload['action'] == 'created';
    }

    public function updateComments(PullRequest $pullRequest)
    {
        $pullRequest->setNumberComments($pullRequest->getNumberComments() + 1);
        if (strpos($this->payload['comment']['body'], '+1') !== false) {
            $pullRequest->setNumberApprovals($pullRequest->getNumberApprovals() + 1);
        }
        if (strpos($this->payload['comment']['body'], '-1') !== false) {
            $pullRequest->setNumberApprovals($pullRequest->getNumberDisapprovals() + 1);
        }
        return $pullRequest;
    }

    public function loadPullRequest($db)
    {
        $pullRequest = $db
            ->getRepository('Src\\Models\\PullRequest')
            ->findOneBy(
                array(
                    'id' => $this->payload['pull_request']['id'],
                    'vcs' => self::VCSNAME
                )
            );
        if ($pullRequest) {
            return $pullRequest;
        }
        return false;
    }
}

<?php

namespace Src\Platforms;

use Src\Models\PullRequest;
use Symfony\Component\HttpFoundation\Request;

class Github extends Vcs implements VcsInterface
{
    const VCSNAME = 'github';

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->id = $this->payload['pull_request']['id'];
        $pullRequest->name = $this->payload['pull_request']['title'];
        $pullRequest->createdBy = $this->payload['pull_request']['user']['login'];
        $pullRequest->createdAt = $this->payload['pull_request']['created_at'];
        $pullRequest->numberComments = 0;
        $pullRequest->numberApprovals = 0;
        $pullRequest->numberDisapprovals = 0;
        $pullRequest->vcs = self::VCSNAME;
        $pullRequest->htmlUrl = $this->payload['pull_request']['html_url'];
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
        $pullRequest->setNumberComments($pullRequest->numberComments++);
        if (strpos($this->payload['comment']['body'], '+1') !== false) {
            $pullRequest->setNumberApprovals($pullRequest->numberApprovals++);
        }
        if (strpos($this->payload['comment']['body'], '-1') !== false) {
            $pullRequest->setNumberApprovals($pullRequest->numberDisapprovals++);
        }
        return $pullRequest;
    }

    public function getPullRequestIdFromPayload()
    {
        return $this->payload['pull_request']['id'];
    }
}

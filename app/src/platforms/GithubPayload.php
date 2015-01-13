<?php

namespace Src\Platforms;

use Src\Entities\PullRequest;
use Symfony\Component\HttpFoundation\Request;

class GithubPayload extends Payload implements PayloadInterface
{
    const VCSNAME = 'github';

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->id = $this->payload['pull_request']['id'];
        $pullRequest->title = $this->payload['pull_request']['title'];
        $pullRequest->createdBy = $this->payload['pull_request']['user']['login'];
        $pullRequest->createdAt = $this->payload['pull_request']['created_at'];
        $pullRequest->numberComments = 0;
        $pullRequest->numberApprovals = 0;
        $pullRequest->numberDisapprovals = 0;
        $pullRequest->repositoryName = $this->payload['repository']['name'];
        $pullRequest->number = $this->payload['number'];
        $pullRequest->vcs = self::VCSNAME;
        $pullRequest->htmlUrl = $this->payload['pull_request']['html_url'];
        $pullRequest->status = 'open';
        return $pullRequest;
    }

    public function isCreatePullRequestPayload()
    {
        return $this->payload['action'] == 'opened' || $this->payload['action'] == 'reopened';
    }

    public function isClosePullRequestPayload()
    {
        return $this->payload['action'] == 'closed';
    }

    public function isCreateCommentPayload()
    {
        return $this->payload['action'] == 'created';
    }

    public function updateComments(PullRequest $pullRequest)
    {
        $pullRequest->numberComments = $pullRequest->numberComments++;
        if (strpos($this->payload['comment']['body'], '+1') !== false) {
            $pullRequest->numberApprovals = $pullRequest->numberApprovals++;
        }
        if (strpos($this->payload['comment']['body'], '-1') !== false) {
            $pullRequest->numberDisapprovals = $pullRequest->numberDisapprovals++;
        }
        return $pullRequest;
    }

    public function getPullRequestIdFromPayload()
    {
        return $this->payload['pull_request']['id'];
    }

    public function setClosed(PullRequest $pullRequest)
    {
        $pullRequest->status = 'closed';
        $pullRequest->updatedAt = $this->payload['pull_request']['closed_at'];
        return $pullRequest;
    }
}

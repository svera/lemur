<?php

namespace Src\Platforms;

use Src\Models\PullRequest;
use Symfony\Component\HttpFoundation\Request;

class Gitlab extends Vcs implements VcsInterface
{
    const VCSNAME = 'gitlab';

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->id = $this->payload['object_attributes']['id'];
        $pullRequest->name = $this->payload['object_attributes']['title'];
        $pullRequest->createdBy = $this->payload['user']['name'];
        $pullRequest->createdAt = $this->payload['object_attributes']['created_at'];
        $pullRequest->vcs = self::VCSNAME;
        $pullRequest->htmlUrl = $this->payload['object_attributes']['source']['http_url'];
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

    /**
     * Gitlab doesn't provide any comment info in webhooks right now,
     * so this method just returns false
     * @param  PullRequest $pullRequest
     * @return bool
     */
    public function isCommentCreatedAction()
    {
        return false;
    }

    /**
     * Gitlab doesn't provide any comment info in webhooks right now,
     * so this method just returns false
     * @param  PullRequest $pullRequest
     * @return bool
     */
    public function updateComments(PullRequest $pullRequest)
    {
        return false;
    }

    public function getPullRequestIdFromPayload()
    {
        return $this->payload['object_attributes']['id'];
    }
}

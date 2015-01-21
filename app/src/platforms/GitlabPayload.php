<?php

namespace Src\Platforms;

use Src\Entities\PullRequest;
use Symfony\Component\HttpFoundation\Request;

final class GitlabPayload extends Payload implements PayloadInterface
{
    const VCSNAME = 'gitlab';

    public function createPullRequest()
    {
        $pullRequest = new PullRequest();
        $pullRequest->id = $this->payload['object_attributes']['id'];
        $pullRequest->title = $this->payload['object_attributes']['title'];
        $pullRequest->createdBy = $this->payload['user']['name'];
        $pullRequest->createdAt = $this->payload['object_attributes']['created_at'];
        $pullRequest->repositoryId = $this->payload['object_attributes']['target_project_id'];
        $pullRequest->repositoryName = $this->payload['object_attributes']['target']['name'];
        $pullRequest->number = $this->payload['object_attributes']['id'];
        $pullRequest->vcs = self::VCSNAME;
        $pullRequest->htmlUrl = $this->payload['object_attributes']['source']['http_url'];
        $pullRequest->status = 'open';
        return $pullRequest;
    }

    public function isCreatePullRequestPayload()
    {
        return $this->payload['object_attributes']['state'] == 'opened';
    }

    public function isClosePullRequestPayload()
    {
        return $this->payload['object_attributes']['state'] == 'closed';
    }

    /**
     * Gitlab doesn't provide any comment info in webhooks right now,
     * so this method just returns false
     * @param  PullRequest $pullRequest
     * @return bool
     */
    public function isCreateCommentPayload()
    {
        return false;
    }

    /**
     * Gitlab doesn't provide any comment info in webhooks right now,
     * so this method just returns false
     * @return bool
     */
    public function getComment()
    {
        return false;
    }

    public function getPullRequestNumber()
    {
        return $this->payload['object_attributes']['id'];
    }

    public function getRepositoryId()
    {
        return $this->payload['object_attributes']['target_project_id'];
    }

    public function getEventDateTime()
    {
        return $this->payload['updated_at'];
    }
}

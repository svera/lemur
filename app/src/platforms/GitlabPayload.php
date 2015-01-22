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
        if (array_key_exists('object_attributes', $this->payload)) {
            return $this->payload['object_attributes']['state'] == 'opened';
        }
        return false;
    }

    public function isClosePullRequestPayload()
    {
        if (array_key_exists('object_attributes', $this->payload)) {
            return $this->payload['object_attributes']['state'] == 'closed';
        }
        return false;
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
        if (array_key_exists('object_attributes', $this->payload)) {
            return $this->payload['object_attributes']['id'];
        }
        return false;
    }

    public function getRepositoryId()
    {
        if (array_key_exists('object_attributes', $this->payload)) {
            return $this->payload['object_attributes']['target_project_id'];
        }
        return false;
    }

    public function getEventDateTime()
    {
        if (array_key_exists('updated_at', $this->payload)) {
            return $this->payload['updated_at'];
        }
        return false;
    }
}

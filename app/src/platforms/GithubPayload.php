<?php
/**
 * @version Github Enterprise 11.10.348
 */

namespace Src\Platforms;

use Src\Entities\PullRequest;
use Symfony\Component\HttpFoundation\Request;

final class GithubPayload extends Payload implements PayloadInterface
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
        $pullRequest->repositoryId = $this->payload['repository']['id'];
        $pullRequest->repositoryName = $this->payload['repository']['name'];
        $pullRequest->number = $this->payload['number'];
        $pullRequest->vcs = self::VCSNAME;
        $pullRequest->htmlUrl = $this->payload['pull_request']['html_url'];
        $pullRequest->status = 'open';
        return $pullRequest;
    }

    public function isCreatePullRequestPayload()
    {
        if (array_key_exists('action', $this->payload)) {
            return $this->payload['action'] == 'opened' || $this->payload['action'] == 'reopened';
        }
        return false;
    }

    public function isClosePullRequestPayload()
    {
        if (array_key_exists('action', $this->payload)) {
            return $this->payload['action'] == 'closed';
        }
        return false;
    }

    public function isCreateCommentPayload()
    {
        return
            $this->headers->get('x-github-event') == 'pull_request_review_comment' ||
            $this->headers->get('x-github-event') == 'issue_comment' ||
            $this->headers->get('x-github-event') == 'commit_comment';
    }

    public function getComment()
    {
        if (array_key_exists('comment', $this->payload)) {
            return $this->payload['comment']['body'];
        }
        return false;
    }

    public function getRepositoryId()
    {
        if (array_key_exists('repository', $this->payload)) {
            return $this->payload['repository']['id'];
        }
        return false;
    }

    public function getPullRequestNumber()
    {
        switch ($this->headers->get('x-github-event'))
        {
            case 'issue_comment':
                return (int)$this->payload['issue']['number'];
            case 'pull_request_review_comment':
                return (int)substr(
                    $this->payload['comment']['pull_request_url'],
                    strrpos($this->payload['comment']['pull_request_url'], '/') + 1
                );
            default:
                return (int)$this->payload['pull_request']['number'];
        }
        return null;
    }

    public function getEventDateTime()
    {
        if ($this->isClosePullRequestPayload()) {
            return $this->payload['pull_request']['closed_at'];
        }
        return $this->payload['pull_request']['updated_at'];
    }
}

<?php

namespace Src\Platforms;

use Symfony\Component\HttpFoundation\Request;
use Src\Models\PullRequest;

interface VcsInterface
{
    /**
     * Creates a new PullRequest object using the received payload
     * @return PullRequest
     */
    public function createPullRequest();

    /**
     * Update the PR comment info using the received payload
     * @return PullRequest
     */
    public function updateComments(PullRequest $pullRequest);

    /**
     * Loads from the database the PR referenced on the payload
     * @return PullRequest
     */
    public function loadPullRequest($db);

    /**
     * Returns true if the current payload refers to a newly created/reopened
     * PR, false otherwise
     * @return boolean
     */
    public function isCreatePullRequestAction();

    /**
     * Returns true if the current payload refers to a closed
     * PR, false otherwise
     * @return boolean
     */
    public function isClosePullRequestAction();

    /**
     * Returns true if the current payload refers to a newly created
     * comment in a PR, false otherwise
     * @return boolean
     */
    public function isCommentCreatedAction();
}

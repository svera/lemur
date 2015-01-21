<?php

namespace Src\Platforms;

use Symfony\Component\HttpFoundation\Request;
use Src\Entities\PullRequest;

interface PayloadInterface
{
    /**
     * Creates a new PullRequest object using the received payload
     * @return PullRequest
     */
    public function createPullRequest();

    /**
     * Get payload comment
     * @return string
     */
    public function getComment();

    /**
     * Returns true if the current payload refers to a newly created/reopened
     * PR, false otherwise
     * @return boolean
     */
    public function isCreatePullRequestPayload();

    /**
     * Returns true if the current payload refers to a closed
     * PR, false otherwise
     * @return boolean
     */
    public function isClosePullRequestPayload();

    /**
     * Returns true if the current payload refers to a newly created
     * comment in a PR, false otherwise
     * @return boolean
     */
    public function isCreateCommentPayload();

    /**
     * Returns pull request number
     * @return integer
     */
    public function getPullRequestNumber();

    /**
     * Returns repository Id
     * @return integer
     */
    public function getRepositoryId();

    /**
     * Marks a pull request as closed
     */
    public function getEventDateTime();
}

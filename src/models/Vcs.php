<?php

namespace Src\Models;

interface Vcs
{
    public function __construct($payload);
    public function createPullRequest();
    public function updateComments(PullRequest $pullRequest);
    public function loadPullRequest($db);
    public function isCreatePullRequestAction();
    public function isClosePullRequestAction();
    public function isCommentCreatedAction();
}

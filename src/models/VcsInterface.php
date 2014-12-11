<?php

namespace Src\Models;

use Symfony\Component\HttpFoundation\Request;

interface VcsInterface
{
    public function createPullRequest();
    public function updateComments(PullRequest $pullRequest);
    public function loadPullRequest($db);
    public function isCreatePullRequestAction();
    public function isClosePullRequestAction();
    public function isCommentCreatedAction();
}

<?php

namespace Src\Platforms;

use Src\Platforms\Github;
use Src\Platforms\Gitlab;
use Symfony\Component\HttpFoundation\Request;

class PayloadFactory
{
    public static function create($vcsName, Request $httpRequest)
    {
        if ($vcsName == 'github') {
            return new GithubPayload($httpRequest);
        }
        if ($vcsName == 'gitlab') {
            return new GitlabPayload($httpRequest);
        }
    }
}

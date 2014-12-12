<?php

namespace Src\Platforms;

use Src\Platforms\Github;
use Src\Platforms\Gitlab;
use Symfony\Component\HttpFoundation\Request;

class VcsFactory
{
    public static function create($vcsName, Request $httpRequest)
    {
        if ($vcsName == 'github') {
            return new Github($httpRequest);
        }
        if ($vcsName == 'gitlab') {
            return new Gitlab($httpRequest);
        }
    }
}

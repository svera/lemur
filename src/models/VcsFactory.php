<?php

namespace Src\Models;

use Src\Models\Github;
use Symfony\Component\HttpFoundation\Request;

class VcsFactory
{
    public static function create($vcsName, Request $httpRequest)
    {
        if ($vcsName == 'github') {
            return new Github($httpRequest);
        }
    }
}

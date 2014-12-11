<?php

namespace Src\Models;

use Symfony\Component\HttpFoundation\Request;

abstract class Vcs
{
    protected $payload;
    
    public function __construct(Request $httpRequest)
    {
        $this->payload = $httpRequest->request->all();
    }
}

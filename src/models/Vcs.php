<?php

namespace Src\Models;

use Symfony\Component\HttpFoundation\Request;

abstract class Vcs
{
    /**
     * Decoded JSON payload, stored as an array
     * @var array
     */
    protected $payload;
    
    /**
     * Gets the payload from the passed request
     * @param Request $httpRequest
     */
    public function __construct(Request $httpRequest)
    {
        $this->payload = $httpRequest->request->all();
    }
}

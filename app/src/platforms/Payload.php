<?php

namespace Src\Platforms;

use Symfony\Component\HttpFoundation\Request;

abstract class Payload
{
    /**
     * Decoded JSON payload, stored as an array
     * @var array
     */
    protected $payload;
    
    /**
     * Request headers
     * @var array
     */
    protected $headers;

    /**
     * Gets the payload from the passed request
     * @param Request $httpRequest
     */
    public function __construct(Request $httpRequest)
    {
        $this->payload = $httpRequest->request->all();
        $this->headers = $httpRequest->headers->all();
    }
}

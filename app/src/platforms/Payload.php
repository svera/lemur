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
     * @var ParameterBag
     */
    protected $headers;

    /**
     * Gets the payload from the passed request
     * @param Request $httpRequest
     */
    public function __construct(Request $httpRequest)
    {
        $this->payload = $httpRequest->request->all();
        $this->headers = $httpRequest->headers;
    }

    /**
     * Returns payload type
     * @return string
     */
    public function getType()
    {
        if ($this->isCreatePullRequestPayload()) {
            return 'pull-request-open';
        }
        if ($this->isClosePullRequestPayload()) {
            return 'pull-request-close';
        }
        if ($this->isCreateCommentPayload()) {
            return 'pull-request-comment';
        }
        if ($this->isPingPayload()) {
            return 'ping';
        }
    }

    /**
     * Return wether a payload is a ping one or not
     * @return boolean
     */
    public function isPingPayload()
    {
        return false;
    }
}

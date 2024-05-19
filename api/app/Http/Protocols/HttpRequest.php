<?php

namespace App\Http\Protocols;

class HttpRequest
{
    public array $body;

    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}

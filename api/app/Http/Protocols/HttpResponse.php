<?php

namespace App\Http\Protocols;

class HttpResponse
{
    public int $statusCode;
    public mixed $body;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function setBody(mixed $body): void
    {
        $this->body = $body;
    }
}

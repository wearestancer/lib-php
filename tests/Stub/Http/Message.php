<?php

namespace ild78\Stub\Http;

use ild78;

class Message
{
    use ild78\Http\MessageTrait;

    public function __construct(
        string $body = null,
        array $headers = [],
        string $version = '1.1'
    ) {
        $this->body = $body;
        $this->protocol = $version;

        foreach ($headers as $name => $value) {
            $this->headers[$name] = (array) $value;
        }
    }
}

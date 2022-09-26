<?php

namespace Stancer\Stub\Http;

use Stancer;
use Psr;

class Message implements Psr\Http\Message\MessageInterface
{
    use Stancer\Http\MessageTrait;

    public function __construct(
        $body = null,
        array $headers = [],
        string $version = '1.1'
    ) {
        $this->protocol = $version;

        if ($body instanceof Psr\Http\Message\StreamInterface) {
            $this->body = $body;
        } else {
            $this->body = new Stancer\Http\Stream($body ?? '');
        }

        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
    }
}

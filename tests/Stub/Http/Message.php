<?php

namespace ild78\Stub\Http;

use ild78;
use Psr;

class Message implements Psr\Http\Message\MessageInterface
{
    use ild78\Http\MessageTrait;

    public function __construct(
        $body = null,
        array $headers = [],
        string $version = '1.1'
    ) {
        $this->protocol = $version;

        if ($body instanceof Psr\Http\Message\StreamInterface) {
            $this->body = $body;
        } else {
            $this->body = new ild78\Http\Stream($body ?? '');
        }

        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
    }
}

<?php

namespace Stancer\Stub;

class JsonSerialize implements \JsonSerializable
{
    public function __construct(protected string $text) {}

    public function jsonSerialize(): mixed
    {
        return $this->text;
    }
}

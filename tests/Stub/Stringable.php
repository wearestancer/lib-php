<?php

namespace Stancer\Stub;

class Stringable implements \Stringable
{
    public function __construct(protected string $text)
    {
    }

    public function __toString(): string
    {
        return $this->text;
    }
}

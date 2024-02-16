<?php

namespace Stancer\Stub;

use Stancer;

#[Stancer\WillChange\PHP8_1\Enumeration]
class FakeStatus
{
    public const ACTIVE = 'active';
    public const DONE = 'done';
    public const PENDING = 'pending';
}

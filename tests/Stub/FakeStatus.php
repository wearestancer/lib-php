<?php

namespace Stancer\Stub;

enum FakeStatus: string
{
    case ACTIVE = 'active';
    case DONE = 'done';
    case PENDING = 'pending';
}

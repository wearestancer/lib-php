<?php

namespace Stancer\Stub\Core;

use Stancer;

class StubObjectV1 extends Stancer\Core\AbstractObject
{
    use Stancer\Stub\TestMethodTrait;
    final public const API_VERSION = Stancer\Enum\ApiVersion::VERSION_2;
}

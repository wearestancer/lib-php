<?php

declare(strict_types=1);

namespace Stancer\ThreeDomainsSecure;

/**
 * List of a 3-D Secure status.
 */
enum Status: string
{
    case NONE = 'none';
    case REQUIRED = 'required';
}

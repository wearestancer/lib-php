<?php
declare(strict_types=1);

namespace Stancer\Http\Verb;

/**
 * HTTP GET.
 */
class Get extends AbstractVerb
{
    protected bool $isAllowed = true;
}

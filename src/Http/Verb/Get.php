<?php
declare(strict_types=1);

namespace Stancer\Http\Verb;

/**
 * HTTP GET.
 */
class Get extends AbstractVerb
{
    /** @var boolean */
    protected $isAllowed = true;
}

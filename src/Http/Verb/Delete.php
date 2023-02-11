<?php
declare(strict_types=1);

namespace Stancer\Http\Verb;

/**
 * HTTP DELETE.
 */
class Delete extends AbstractVerb
{
    /** @var boolean */
    protected $isAllowed = true;
}

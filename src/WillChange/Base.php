<?php
declare(strict_types=1);

namespace Stancer\WillChange;

/**
 * Base class for migration attributes.
 */
class Base
{
    /**
     * Constructor.
     *
     * @param string|null $comment Arbitrary comment to help define what will change.
     */
    public function __construct(protected ?string $comment = null)
    {
    }
}

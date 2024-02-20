<?php
declare(strict_types=1);

namespace Stancer\WillChange;

/**
 * Base class for migration attributes.
 */
#[PHP8_0\ConstructorPropertyPromotion]
class Base
{
    /** @var string|null */
    protected ?string $comment;

    /**
     * Constructor.
     *
     * @param string|null $comment Arbitrary comment to help define what will change.
     */
    public function __construct(?string $comment = null)
    {
        $this->comment = $comment;
    }
}

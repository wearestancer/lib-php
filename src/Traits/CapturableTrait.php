<?php

declare(strict_types=1);

namespace Stancer\Traits;

/**
 * Simple trait to handle everything around amounts.
 */
trait CapturableTrait
{
    /**
     * Return a boolean telling if the payment is capturable.
     *
     * @return boolean
     */
    public function isCapturable(): bool
    {
        return match ($this) {
            static::AUTHORIZED => true,
            default => false,
        };
    }
}

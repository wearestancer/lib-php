<?php

declare(strict_types=1);

namespace Stancer\Http\Verb;

/**
 * Abstraction for every verb.
 */
abstract class AbstractVerb
{
    /** @var boolean Is this verb allowed? */
    protected bool $isAllowed = false;

    /**
     * Return the HTTP verb.
     */
    public function __toString(): string
    {
        $class = static::class;
        $namespace = __NAMESPACE__ . '\\';

        return strtoupper(str_replace($namespace, '', $class));
    }

    /**
     * Indicate if the verb is allowed on the API.
     *
     * @return boolean
     */
    public function isAllowed(): bool
    {
        return $this->isAllowed;
    }

    /**
     * Indicate if the verb is not allowed on the API.
     *
     * @return boolean
     */
    public function isNotAllowed(): bool
    {
        return !$this->isAllowed();
    }
}

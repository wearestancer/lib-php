<?php
declare(strict_types=1);

namespace ild78\Http\Verb;

/**
 * Abstraction for every verb
 */
abstract class AbstractVerb
{
    /**
     * Return the HTTP verb
     *
     * @return string
     */
    public function __toString() : string
    {
        $class = static::class;
        $namespace = __NAMESPACE__ . '\\';

        return strtoupper(str_replace($namespace, '', $class));
    }
}

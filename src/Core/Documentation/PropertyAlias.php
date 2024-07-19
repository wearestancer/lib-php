<?php

declare(strict_types=1);

namespace Stancer\Core\Documentation;

use Attribute;

/**
 * Attribute to add an alias on a property on documentation.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class PropertyAlias
{
    /**
     * Create a new alias.
     *
     * @param string $name New property name.
     * @param string $for Property name aliased.
     */
    public function __construct(protected string $name, protected string $for) {}

    /**
     * Return aliased property name.
     */
    public function getAliasedName(): string
    {
        return $this->for;
    }

    /**
     * Return property name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}

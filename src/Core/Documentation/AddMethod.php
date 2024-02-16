<?php
declare(strict_types=1);

namespace Stancer\Core\Documentation;

use Attribute;

/**
 * Attribute to add documentation data on a method.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AddMethod
{
    /**
     * Create a new attribute.
     *
     * @param string $name New method name.
     * @param string[] $parameters Parameters list.
     * @param string $return Return type.
     * @param string $description Method description.
     * @param boolean $stan Is a phpstan phpdoc?.
     */
    public function __construct(
        protected string $name,
        protected array $parameters,
        protected string $return,
        protected ?string $description = null,
        protected bool $stan = false,
    ) {
    }

    /**
     * Return phpdoc data.
     *
     * @return DocumentationMethodParameters
     */
    public function getData(): array
    {
        return [
            'method' => [
                'desc' => $this->description,
                'name' => $this->name,
                'parameters' => $this->parameters,
                'return' => $this->return,
                'stan' => $this->stan,
            ],
        ];
    }
}

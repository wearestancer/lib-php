<?php
declare(strict_types=1);

namespace Stancer\Core\Documentation;

use Attribute;
use Stancer;

/**
 * Attribute to add documentation data on a property.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AddProperty
{
    /**
     * Create a new attribute.
     *
     * @param string $name New property name.
     * @param string $description Property description.
     * @param string $fullDescription Property full description (without manipulation from the phpdoc script).
     * @param boolean $generateMethodGetter Do we need to create an entry for a get method?.
     * @param boolean $list Is this parameter a list?.
     * @param boolean $nullable Is it nullable?.
     * @param boolean $required Is it required?.
     * @param boolean $restricted Is it restricted (aka no setter)?.
     * @param string|string[] $type Property types.
     * @param mixed $value Property default value.
     */
    #[Stancer\WillChange\PHP8_0\UnionType]
    #[Stancer\WillChange\PHP8_0\MixedType]
    public function __construct(
        protected string $name,
        protected ?string $description = null,
        protected ?string $fullDescription = null,
        protected bool $generateMethodGetter = true,
        protected bool $list = false,
        protected bool $nullable = true,
        protected bool $required = false,
        protected bool $restricted = false,
        protected $type = Stancer\Core\AbstractObject::STRING,
        protected $value = null,
    ) {
    }

    /**
     * Return phpdoc data.
     *
     * @return DocumentationPropertyParameters
     */
    public function getData(): array
    {
        return [
            'desc' => $this->description,
            'fullDesc' => $this->fullDescription,
            'generateMethodGetter' => $this->generateMethodGetter,
            'list' => $this->list,
            'nullable' => $this->nullable,
            'required' => $this->required,
            'restricted' => $this->restricted,
            'type' => $this->type,
            'value' => $this->value,
        ];
    }

    /**
     * Return property name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

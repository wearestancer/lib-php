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
     * @param string|null $description Property description.
     * @param string|null $fullDescription Property full description (without manipulation from the phpdoc script).
     * @param array|false|null $getter Getter data.
     * @param boolean|null $list Is this parameter a list?.
     * @param boolean|null $nullable Is it nullable?.
     * @param array|false|null $property Property data.
     * @param boolean|null $required Is it required?.
     * @param boolean|null $restricted Is it restricted (aka no setter)?.
     * @param array|false|null $setter Setter data.
     * @param string|string[] $type Property types.
     * @param mixed $value Property default value.
     *
     * @phpstan-param DocumentationPropertyParameters|false|null $getter
     * @phpstan-param DocumentationPropertyParameters|false|null $property
     * @phpstan-param DocumentationPropertyParameters|false|null $setter
     */
    public function __construct(
        protected string $name,
        protected ?string $description = null,
        protected ?string $fullDescription = null,
        protected array|false|null $getter = null,
        protected ?bool $list = null,
        protected ?bool $nullable = null,
        protected array|false|null $property = null,
        protected ?bool $required = null,
        protected ?bool $restricted = null,
        protected array|false|null $setter = null,
        protected string|array|null $type = null,
        protected mixed $value = null,
    ) {
    }

    /**
     * Return phpdoc data.
     *
     * @return DocumentationPropertyParameters
     */
    public function getData(): array
    {
        $data = [];

        if (!is_null($this->description)) {
            $data['desc'] = $this->description;
        }

        if (!is_null($this->fullDescription)) {
            $data['fullDesc'] = $this->fullDescription;
        }

        if (!is_null($this->getter)) {
            $data['getter'] = $this->getter;
        }

        if (!is_null($this->list)) {
            $data['list'] = $this->list;
        }

        if (!is_null($this->nullable)) {
            $data['nullable'] = $this->nullable;
        }

        if ($this->property) {
            $data['property'] = $this->property;
        }

        if (!is_null($this->required)) {
            $data['required'] = $this->required;
        }

        if (!is_null($this->restricted)) {
            $data['restricted'] = $this->restricted;
        }

        if ($this->setter) {
            $data['setter'] = $this->setter;
        }

        if (!is_null($this->type)) {
            $data['type'] = $this->type;
        }

        if (!is_null($this->value)) {
            $data['value'] = $this->value;
        }

        return $data;
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

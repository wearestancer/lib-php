<?php
declare(strict_types=1);

namespace Stancer\Core\Documentation;

use Attribute;
use Stancer;

/**
 * Attribute to add documentation data on a property or a method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class FormatProperty
{
    /**
     * Create a new attribute.
     *
     * @param string|null $description Property description.
     * @param string|null $fullDescription Property full description (without manipulation from the phpdoc script).
     * @param boolean|null $generateMethodGetter Do we need to create an entry for a get method?.
     * @param boolean|null $list Is this parameter a list?.
     * @param boolean|null $nullable Is it nullable?.
     * @param boolean|null $required Is it required?.
     * @param boolean|null $restricted Is it restricted (aka no setter)?.
     * @param string|string[] $type Property types.
     * @param mixed $value Property default value.
     */
    #[Stancer\WillChange\PHP8_0\UnionType]
    #[Stancer\WillChange\PHP8_0\MixedType]
    public function __construct(
        protected ?string $description = null,
        protected ?string $fullDescription = null,
        protected ?bool $generateMethodGetter = null,
        protected ?bool $list = null,
        protected ?bool $nullable = null,
        protected ?bool $required = null,
        protected ?bool $restricted = null,
        protected $type = null,
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
        $data = [];

        if (!is_null($this->description)) {
            $data['desc'] = $this->description;
        }

        if (!is_null($this->fullDescription)) {
            $data['fullDesc'] = $this->fullDescription;
        }

        if (!is_null($this->generateMethodGetter)) {
            $data['generateMethodGetter'] = $this->generateMethodGetter;
        }

        if (!is_null($this->list)) {
            $data['list'] = $this->list;
        }

        if (!is_null($this->nullable)) {
            $data['nullable'] = $this->nullable;
        }

        if (!is_null($this->required)) {
            $data['required'] = $this->required;
        }

        if (!is_null($this->restricted)) {
            $data['restricted'] = $this->restricted;
        }

        if (!is_null($this->type)) {
            $data['type'] = $this->type;
        }

        if (!is_null($this->value)) {
            $data['value'] = $this->value;
        }

        return $data;
    }
}

<?php

namespace Stancer\Stub;

use Stancer;

trait TestMethodTrait {
    public function __set(string $property, $value): void
    {
        $prop = Stancer\Helper::snakeCaseToCamelCase($property);
        $method = 'set' . $prop;

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }

        if (array_key_exists($prop, $this->dataModel)) {
            $this->{$method}($value);
        }
    }

    public function testOnlyAddModified(string $modified): Stancer\Core\AbstractObject
    {
        $this->modified[] = $modified;

        return $this;
    }

    public function testOnlySetId(string $id): Stancer\Core\AbstractObject
    {
        $this->id = $id;

        return $this;
    }

    public function testOnlyGetModified(): array
    {
        return $this->modified;
    }

    public function testOnlyGetPopulated(): bool
    {
        return $this->populated;
    }

    public function testOnlyResetModified(): Stancer\Core\AbstractObject
    {
        $this->modified = [];

        return $this;
    }

    public function testOnlySetPopulated(bool $populated): Stancer\Core\AbstractObject
    {
        $this->populated = $populated;

        return $this;
    }
}

<?php

namespace ild78\Stub;

use ild78;

trait TestMethodTrait {
    public function testOnlyAddModified(string $modified): ild78\Core\AbstractObject
    {
        $this->modified[] = $modified;

        return $this;
    }

    public function testOnlySetId(string $id): ild78\Core\AbstractObject
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

    public function testOnlyResetModified(): ild78\Core\AbstractObject
    {
        $this->modified = [];

        return $this;
    }

    public function testOnlySetPopulated(bool $populated): ild78\Core\AbstractObject
    {
        $this->populated = $populated;

        return $this;
    }
}

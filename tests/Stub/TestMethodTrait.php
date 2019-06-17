<?php

namespace ild78\Stub;

use ild78;

trait TestMethodTrait {
    public function testOnlySetId(string $id) : ild78\Api\AbstractObject
    {
        $this->id = $id;

        return $this;
    }

    public function testOnlyGetPopulated() : bool
    {
        return $this->populated;
    }

    public function testOnlySetModified(bool $modified) : ild78\Api\AbstractObject
    {
        $this->modified = $modified;

        return $this;
    }

    public function testOnlySetPopulated(bool $populated) : ild78\Api\AbstractObject
    {
        $this->populated = $populated;

        return $this;
    }
}

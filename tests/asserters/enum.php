<?php

namespace Stancer\Tests\asserters;

use atoum;

class enum extends atoum\atoum\asserters\phpClass
{
    protected bool $isSet = false;
    protected mixed $value = null;

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'isequalto':
            case 'isidenticalto':
            case 'isnotequalto':
            case 'isnotidenticalto':
                return $this->{$property}();

            default:
                return parent::__get($property);
        }
    }

    public function isEqualTo(mixed $value, string $failMessage = null): self
    {
        if ($this->valueIsSet()->value == $value) {
            return $this->pass();
        }

        return $this->fail($failMessage ?: $this->_('enum are not equal'));
    }

    public function isIdenticalTo(mixed $value, string $failMessage = null): self
    {
        if ($this->valueIsSet()->value === $value) {
            return $this->pass();
        }

        return $this->fail($failMessage ?: $this->_('enum are not identical'));
    }

    public function isNotEqualTo(mixed $value, string $failMessage = null): self
    {
        if ($this->valueIsSet()->value != $value) {
            return $this->pass();
        }

        return $this->fail($failMessage ?: $this->_('enum are equal'));
    }

    public function isNotIdenticalTo(mixed $value, string $failMessage = null): self
    {
        if ($this->valueIsSet()->value !== $value) {
            return $this->pass();
        }

        return $this->fail($failMessage ?: $this->_('enum are identical'));
    }

    public function setWith(mixed $value): self
    {
        parent::setWith($value);

        $this->value = $value;
        $this->isSet = true;

        if ($this->class->isEnum() === true) {
            return $this->pass();
        }

        return $this->fail($this->_('%s is not an enum', $this));
    }

    protected function valueIsSet(string $message = 'Value is undefined'): self
    {
        if ($this->isSet === false) {
            throw new atoum\atoum\exceptions\logic($message);
        }

        return $this;
    }
}

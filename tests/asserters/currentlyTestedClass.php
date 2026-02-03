<?php

namespace Stancer\Tests\asserters;

use atoum;

class currentlyTestedClass extends atoum\atoum\asserters\testedClass
{
    public function constant(string $constant, ?string $failMessage = null)
    {
        $asserter = parent::hasConstant($constant, $failMessage);

        if ($this->classIsSet()->class->isEnum() === true) {
            return $this->generator->constant($this->class->getConstant($constant)->value);
        }

        return $asserter;
    }

    public function hasConstant($constant, $failMessage = null)
    {
        parent::hasConstant($constant, $failMessage);

        return $this;
    }

    public function hasTrait(string $trait, ?string $failMessage = null)
    {
        try {
            $traits = $this->classIsSet()->class->getTraits();

            if (array_key_exists($trait, $traits)) {
                $this->pass();
            } else {
                $this->fail($failMessage ?? $this->_('%s does not uses trait %s', $this, $trait));
            }
        } catch (\ReflectionException $exception) {
            throw new atoum\atoum\exceptions\logic('Argument of ' . __METHOD__ . '() must be a trait', previous: $exception);
        }

        return $this;
    }

    public function isBackedEnum(?string $failMessage = null): self
    {
        $className = $this->isEnum($failMessage)->class->getName();

        if (is_a($className, \BackedEnum::class, true)) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not a backed enum', $this));
        }

        return $this;
    }

    public function isEnum(?string $failMessage = null): self
    {
        if ($this->classIsSet()->class->isEnum() === true) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not an enum', $this));
        }

        return $this;
    }
}

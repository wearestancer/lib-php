<?php

namespace Stancer\Tests\asserters;

use atoum;

class currentlyTestedClass extends atoum\atoum\asserters\testedClass
{
    public function constant($constant, $failMessage = null)
    {
        return parent::hasConstant($constant, $failMessage);
    }

    public function hasConstant($constant, $failMessage = null)
    {
        parent::hasConstant($constant, $failMessage);

        return $this;
    }

    public function hasTrait($trait, $failMessage = null)
    {
        try {
            $traits = $this->classIsSet()->class->getTraits();

            if (array_key_exists($trait, $traits)) {
                $this->pass();
            } else {
                $this->fail($failMessage ?? $this->_('%s does not uses trait %s', $this, $trait));
            }
        } catch (\reflectionException $exception) {
            throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a trait', null, $exception);
        }

        return $this;
    }
}

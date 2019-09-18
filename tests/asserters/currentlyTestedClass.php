<?php

namespace ild78\Tests\asserters;

use atoum;

class currentlyTestedClass extends atoum\asserters\testedClass
{
    public function hasTrait($trait, $failMessage = null)
    {
        try {
            $traits = $this->classIsSet()->class->getTraits();

            if (array_key_exists($trait, $traits)) {
                $this->pass();
            } else {
                $this->fail($failMessage ?: $this->_('%s does not uses trait %s', $this, $trait));
            }
        } catch (\reflectionException $exception) {
            throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a trait', null, $exception);
        }

        return $this;
    }
}

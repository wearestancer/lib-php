<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Stream extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->implements(Psr\Http\Message\StreamInterface::class)
        ;
    }
}

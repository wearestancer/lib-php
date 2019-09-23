<?php

namespace ild78\tests\unit;

use ild78;

class Auth extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Api\AbstractObject::class)
        ;
    }
}

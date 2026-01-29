<?php

namespace Stancer\Tests\unit\Stub\Core;

use Stancer;

class StubObjectV1 extends Stancer\Tests\atoum
{
    public function test_version()
    {
        $config = Stancer\Config::getGlobal();
        $this
            ->given($config->setVersion(Stancer\Enum\ApiVersion::VERSION_1))
            ->assert('new tested instance fail in V1')
                ->exception(function () {
                    $this->newTestedInstance;
                })
                    ->isInstanceOf(Stancer\Exceptions\BadApiVersionException::class)
                    ->message
                        ->isIdenticalTo('Stancer\\Stub\\Core\\StubObjectV1 is compatible with API V2 and up.')
            ->given($config->setVersion(Stancer\Enum\ApiVersion::VERSION_2))
            ->assert('new tested instance init in V2')
                ->given($this->newTestedInstance)
        ;
    }
}

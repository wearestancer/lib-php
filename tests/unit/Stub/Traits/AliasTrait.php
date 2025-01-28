<?php

namespace Stancer\tests\unit\Stub\Traits;

use Stancer;
use Stancer\Stub\Traits\AliasTrait as testedClass;

class AliasTrait extends Stancer\Tests\atoum
{
    public function test__call_aliases()
    {
        $this
            ->assert('camelCase method')
                ->integer($this->newTestedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->aliasedMethod())
                    ->isTestedInstance

                ->integer($this->testedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(1)

            ->assert('snake_case method')
                ->integer($this->newTestedInstance->fake_method_call_count())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->aliased_method())
                    ->isTestedInstance

                ->integer($this->testedInstance->fake_method_call_count())
                    ->isIdenticalTo(1)
        ;
    }

    public function test__call_changeCase()
    {
        $this
            ->assert('camelCase method')
                ->integer($this->newTestedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->fakeMethod())
                    ->isTestedInstance

                ->integer($this->testedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(1)

            ->assert('snake_case method')
                ->integer($this->newTestedInstance->fake_method_call_count())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->fake_method())
                    ->isTestedInstance

                ->integer($this->testedInstance->fake_method_call_count())
                    ->isIdenticalTo(1)
        ;
    }

    public function test__call_exception()
    {
        $this
            ->assert('camelCase method')
                ->exception(fn () => $this->newTestedInstance->unknownMethod())
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('Method "Stancer\\Stub\\Traits\\AliasTrait::unknownMethod()" unknown')

            ->assert('snake_case method')
                ->exception(fn () => $this->newTestedInstance->unknown_method())
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('Method "Stancer\\Stub\\Traits\\AliasTrait::unknown_method()" unknown')
        ;
    }

    public function test__call_getter()
    {
        $this
            ->assert('camelCase method')
                ->integer($this->newTestedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(0)

                ->integer($this->newTestedInstance->getFakeMethodCount())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->aliasedMethod())
                    ->isTestedInstance

                ->integer($this->testedInstance->fakeMethodCallCount())
                    ->isIdenticalTo(1)

                ->integer($this->testedInstance->getFakeMethodCount())
                    ->isIdenticalTo(1)

            ->assert('snake_case method')
                ->integer($this->newTestedInstance->fake_method_call_count())
                    ->isIdenticalTo(0)

                ->integer($this->newTestedInstance->get_fake_method_count())
                    ->isIdenticalTo(0)

                ->object($this->testedInstance->aliased_method())
                    ->isTestedInstance

                ->integer($this->testedInstance->fake_method_call_count())
                    ->isIdenticalTo(1)

                ->integer($this->testedInstance->get_fake_method_count())
                    ->isIdenticalTo(1)
        ;
    }

    public function test__callStatic_changeCase()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('camelCase method')
                    ->integer(testedClass::fakeStaticMethodCallCount())
                        ->isIdenticalTo(0)

                    ->object(testedClass::fakeStaticMethod())
                        ->isInstanceOfTestedClass

                    ->integer(testedClass::fakeStaticMethodCallCount())
                        ->isIdenticalTo(1)

                ->assert('snake_case method')
                    ->integer(testedClass::fake_static_method_call_count())
                        ->isIdenticalTo(1)

                    ->object(testedClass::fake_static_method())
                        ->isInstanceOfTestedClass

                    ->integer(testedClass::fake_static_method_call_count())
                        ->isIdenticalTo(2)
        ;
    }

    public function test__callStatic_exception()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('camelCase method')
                    ->exception(fn () => testedClass::unknownMethod())
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('Method "Stancer\\Stub\\Traits\\AliasTrait::unknownMethod()" unknown')

                ->assert('snake_case method')
                    ->exception(fn () => testedClass::unknown_method())
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('Method "Stancer\\Stub\\Traits\\AliasTrait::unknown_method()" unknown')
        ;
    }
}

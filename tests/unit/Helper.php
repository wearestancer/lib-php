<?php

namespace Stancer\tests\unit;

use Stancer;
use Stancer\Helper as testedClass;

class Helper extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Strings;

    /**
     * @dataProvider caseStringDataProvider
     *
     * @param mixed $camel
     * @param mixed $snake
     */
    public function testCamelCaseToSnakeCase($camel, $snake)
    {
        $this->string(testedClass::camelCaseToSnakeCase($camel))->isIdenticalTo($snake);
    }

    /**
     * @dataProvider caseStringDataProvider
     *
     * @param mixed $camel
     * @param mixed $snake
     */
    public function testSnakeCaseToCamelCase($camel, $snake)
    {
        $this->string(testedClass::snakeCaseToCamelCase($snake))->isIdenticalTo($camel);
    }
}

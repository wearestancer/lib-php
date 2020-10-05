<?php
declare(strict_types=1);

namespace ild78\PHPStan\Types;

use ild78;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\CallableType;
use PHPStan\Type\ClassStringType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class AbstractObjectDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ild78\Core\AbstractObject::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getModel';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        if (count($methodCall->args) === 0) {
            return TypeCombinator::addNull(new ArrayType(new StringType(), new MixedType()));
        }

        $sizeKeys = [
            new ConstantStringType('fixed'),
            new ConstantStringType('max'),
            new ConstantStringType('min'),
        ];
        $sizeValues = [
            TypeCombinator::addNull(new IntegerType()),
            TypeCombinator::addNull(new IntegerType()),
            TypeCombinator::addNull(new IntegerType()),
        ];

        $keys = [
            new ConstantStringType('coerce'),
            new ConstantStringType('exception'),
            new ConstantStringType('exportable'),
            new ConstantStringType('list'),
            new ConstantStringType('required'),
            new ConstantStringType('restricted'),
            new ConstantStringType('size'),
            new ConstantStringType('type'),
            new ConstantStringType('value'),
        ];
        $values = [
            TypeCombinator::addNull(new CallableType()),
            TypeCombinator::addNull(new ClassStringType()),
            new BooleanType(),
            new BooleanType(),
            new BooleanType(),
            new BooleanType(),
            new ConstantArrayType($sizeKeys, $sizeValues),
            new StringType(),
            new MixedType(),
        ];

        return new ConstantArrayType($keys, $values);
    }
}

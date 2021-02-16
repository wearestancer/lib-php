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
use PHPStan\Type\NullType;
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

        $createKey = function ($name): ConstantStringType {
            return new ConstantStringType($name);
        };

        $sizeValues = [
            'fixed' => TypeCombinator::addNull(new IntegerType()),
            'max' => TypeCombinator::addNull(new IntegerType()),
            'min' => TypeCombinator::addNull(new IntegerType()),
        ];
        $sizeKeys = array_map($createKey, array_keys($sizeValues));

        $allowedAsArray = new ArrayType(new IntegerType(), new StringType());
        $allowedAsString = new ClassStringType();

        $values = [
            'allowedValues' => TypeCombinator::union(new NullType(), $allowedAsArray, $allowedAsString),
            'coerce' => TypeCombinator::addNull(new CallableType()),
            'exception' => TypeCombinator::addNull(new ClassStringType()),
            'exportable' => new BooleanType(),
            'format' => TypeCombinator::addNull(new CallableType()),
            'list' => new BooleanType(),
            'required' => new BooleanType(),
            'restricted' => new BooleanType(),
            'size' => new ConstantArrayType($sizeKeys, array_values($sizeValues)),
            'type' => new StringType(),
            'value' => new MixedType(),
        ];
        $keys = array_map($createKey, array_keys($values));

        return new ConstantArrayType($keys, array_values($values));
    }
}

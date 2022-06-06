<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\TypeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\PHPStan\Reflection\ClassReflection;
use RevealPrefix20220606\PHPStan\Reflection\FunctionVariant;
use RevealPrefix20220606\PHPStan\Reflection\ParametersAcceptorSelector;
use RevealPrefix20220606\PHPStan\Type\MixedType;
use RevealPrefix20220606\PHPStan\Type\Type;
use RevealPrefix20220606\Symplify\Astral\Exception\ShouldNotHappenException;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 */
final class ClassMethodReturnTypeResolver
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function resolve(ClassMethod $classMethod, Scope $scope) : Type
    {
        $methodName = $this->simpleNameResolver->getName($classMethod);
        if (!\is_string($methodName)) {
            throw new ShouldNotHappenException();
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return new MixedType();
        }
        $methodReflection = $classReflection->getMethod($methodName, $scope);
        $functionVariant = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
        if (!$functionVariant instanceof FunctionVariant) {
            return new MixedType();
        }
        return $functionVariant->getReturnType();
    }
}

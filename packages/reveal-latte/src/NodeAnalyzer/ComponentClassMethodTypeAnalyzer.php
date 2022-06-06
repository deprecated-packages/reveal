<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\PHPStan\Reflection\ClassReflection;
use RevealPrefix20220606\PHPStan\Type\Type;
use RevealPrefix20220606\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
final class ComponentClassMethodTypeAnalyzer
{
    public function resolveReturnType(ClassMethod $classMethod, Scope $scope) : Type
    {
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            throw new ShouldNotHappenException();
        }
        $methodName = (string) $classMethod->name;
        $methodReflection = $classReflection->getNativeMethod($methodName);
        $parametersAcceptor = $methodReflection->getVariants()[0];
        return $parametersAcceptor->getReturnType();
    }
}

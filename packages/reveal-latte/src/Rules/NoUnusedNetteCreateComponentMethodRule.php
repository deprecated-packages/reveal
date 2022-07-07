<?php

declare (strict_types=1);
namespace Reveal\RevealLatte\Rules;

use RevealPrefix20220707\Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Reveal\RevealLatte\LatteUsedControlResolver;
use Reveal\RevealLatte\NodeAnalyzer\UsedLocalComponentNamesResolver;
use RevealPrefix20220707\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @see \Reveal\RevealLatte\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\NoUnusedNetteCreateComponentMethodRuleTest
 * @implements Rule<ClassMethod>
 */
final class NoUnusedNetteCreateComponentMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The component factory method "%s()" is never used in presenter templates';
    /**
     * @var string
     */
    private const CREATE_COMPONENT = 'createComponent';
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\RevealLatte\NodeAnalyzer\UsedLocalComponentNamesResolver
     */
    private $usedLocalComponentNamesResolver;
    /**
     * @var \Reveal\RevealLatte\LatteUsedControlResolver
     */
    private $latteUsedControlResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver, UsedLocalComponentNamesResolver $usedLocalComponentNamesResolver, LatteUsedControlResolver $latteUsedControlResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->usedLocalComponentNamesResolver = $usedLocalComponentNamesResolver;
        $this->latteUsedControlResolver = $latteUsedControlResolver;
    }
    public function getNodeType() : string
    {
        return ClassMethod::class;
    }
    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }
        $controlName = $this->resolveControlName($node);
        if ($controlName === null) {
            return [];
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return [];
        }
        $localUsedControlMethodNames = $this->usedLocalComponentNamesResolver->resolveFromClassMethod($node, $scope);
        if (\in_array($controlName, $localUsedControlMethodNames, \true)) {
            return [];
        }
        if ($classReflection->isAbstract()) {
            $layoutUsedControlNames = $this->latteUsedControlResolver->resolveLayoutControlNames();
            if (\in_array($controlName, $layoutUsedControlNames, \true)) {
                return [];
            }
        }
        $latteUsedControlNames = $this->latteUsedControlResolver->resolveControlNames($scope);
        if (\in_array($controlName, $latteUsedControlNames, \true)) {
            return [];
        }
        $methodName = $this->simpleNameResolver->getName($node);
        return [\sprintf(self::ERROR_MESSAGE, $methodName)];
    }
    private function resolveControlName(ClassMethod $classMethod) : ?string
    {
        $classMethodName = $this->simpleNameResolver->getName($classMethod->name);
        if ($classMethodName === null) {
            return null;
        }
        if (\strncmp($classMethodName, self::CREATE_COMPONENT, \strlen(self::CREATE_COMPONENT)) !== 0) {
            return null;
        }
        $controlName = (string) Strings::after($classMethodName, self::CREATE_COMPONENT);
        return \lcfirst($controlName);
    }
    private function shouldSkip(Scope $scope, ClassMethod $classMethod) : bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return \true;
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return \true;
        }
        if (!$classReflection->isSubclassOf('RevealPrefix20220707\\Nette\\Application\\UI\\Presenter')) {
            return \true;
        }
        return $classMethod->isPrivate();
    }
}

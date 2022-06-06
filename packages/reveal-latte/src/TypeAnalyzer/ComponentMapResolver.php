<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\TypeAnalyzer;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Stmt\Class_;
use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer\ComponentClassMethodTypeAnalyzer;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use RevealPrefix20220606\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
final class ComponentMapResolver
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\RevealLatte\NodeAnalyzer\ComponentClassMethodTypeAnalyzer
     */
    private $componentClassMethodTypeAnalyzer;
    /**
     * @var \Symplify\Astral\NodeFinder\SimpleNodeFinder
     */
    private $simpleNodeFinder;
    public function __construct(SimpleNameResolver $simpleNameResolver, ComponentClassMethodTypeAnalyzer $componentClassMethodTypeAnalyzer, SimpleNodeFinder $simpleNodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->componentClassMethodTypeAnalyzer = $componentClassMethodTypeAnalyzer;
        $this->simpleNodeFinder = $simpleNodeFinder;
    }
    /**
     * @return ComponentNameAndType[]
     */
    public function resolveFromMethodCall(MethodCall $methodCall, Scope $scope) : array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (!$class instanceof Class_) {
            return [];
        }
        return $this->resolveComponentNamesAndTypes($class, $scope);
    }
    /**
     * @return ComponentNameAndType[]
     */
    public function resolveFromClassMethod(ClassMethod $classMethod, Scope $scope) : array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (!$class instanceof Class_) {
            return [];
        }
        return $this->resolveComponentNamesAndTypes($class, $scope);
    }
    /**
     * @return ComponentNameAndType[]
     */
    public function resolveComponentNamesAndTypes(Class_ $class, Scope $scope) : array
    {
        $componentNamesAndTypes = [];
        foreach ($class->getMethods() as $classMethod) {
            if (!$this->simpleNameResolver->isName($classMethod, 'createComponent*')) {
                continue;
            }
            /** @var string $methodName */
            $methodName = $this->simpleNameResolver->getName($classMethod);
            $componentName = Strings::after($methodName, 'createComponent');
            if ($componentName === null) {
                throw new ShouldNotHappenException();
            }
            $componentName = \lcfirst($componentName);
            $classMethodReturnType = $this->componentClassMethodTypeAnalyzer->resolveReturnType($classMethod, $scope);
            $componentNamesAndTypes[] = new ComponentNameAndType($componentName, $classMethodReturnType);
        }
        return $componentNamesAndTypes;
    }
}

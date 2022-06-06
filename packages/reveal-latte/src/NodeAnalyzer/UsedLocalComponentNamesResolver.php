<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayDimFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Scalar\String_;
use RevealPrefix20220606\PhpParser\Node\Stmt\Class_;
use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PhpParser\NodeFinder;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\ValueObject\AttributeKey;
final class UsedLocalComponentNamesResolver
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    public function __construct(SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
    }
    /**
     * @return string[]
     */
    public function resolveFromClassMethod(ClassMethod $classMethod) : array
    {
        $parent = $classMethod->getAttribute(AttributeKey::PARENT);
        if (!$parent instanceof Class_) {
            return [];
        }
        $getComponentNames = $this->resolveThisGetComponentArguments($parent);
        $dimFetchNames = $this->resolveDimFetchArguments($parent);
        return \array_merge($getComponentNames, $dimFetchNames);
    }
    /**
     * @return string[]
     */
    private function resolveThisGetComponentArguments(Class_ $class) : array
    {
        $componentNames = [];
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($class, MethodCall::class);
        foreach ($methodCalls as $methodCall) {
            if (!$methodCall->var instanceof Variable) {
                continue;
            }
            if (!$this->simpleNameResolver->isName($methodCall->var, 'this')) {
                continue;
            }
            if (!$this->simpleNameResolver->isName($methodCall->name, 'getComponent')) {
                continue;
            }
            $firstArg = $methodCall->args[0] ?? null;
            if (!$firstArg instanceof Arg) {
                continue;
            }
            $firstArgValue = $firstArg->value;
            if (!$firstArgValue instanceof String_) {
                continue;
            }
            $componentNames[] = $firstArgValue->value;
        }
        return $componentNames;
    }
    /**
     * @return string[]
     */
    private function resolveDimFetchArguments(Class_ $class) : array
    {
        $componentNames = [];
        /** @var ArrayDimFetch[] $arrayDimFetches */
        $arrayDimFetches = $this->nodeFinder->findInstanceOf($class, ArrayDimFetch::class);
        foreach ($arrayDimFetches as $arrayDimFetch) {
            if (!$arrayDimFetch->var instanceof Variable) {
                continue;
            }
            if (!$this->simpleNameResolver->isName($arrayDimFetch->var, 'this')) {
                continue;
            }
            if (!$arrayDimFetch->dim instanceof String_) {
                continue;
            }
            $componentNames[] = $arrayDimFetch->dim->value;
        }
        return $componentNames;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\RevealLatte\\NodeAnalyzer\\UsedLocalComponentNamesResolver', 'Reveal\\RevealLatte\\NodeAnalyzer\\UsedLocalComponentNamesResolver', \false);

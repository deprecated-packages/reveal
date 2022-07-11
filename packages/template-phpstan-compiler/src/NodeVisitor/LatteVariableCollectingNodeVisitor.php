<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use Reveal\TemplatePHPStanCompiler\VariableUsage\CreatedVariableNamesResolver;
use RevealPrefix20220711\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 */
final class LatteVariableCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $userVariableNames = [];
    /**
     * @var string[]
     */
    private $createdVariableNames = [];
    /**
     * @var \Reveal\TemplatePHPStanCompiler\VariableUsage\CreatedVariableNamesResolver
     */
    private $createdVariableNamesResolver;
    /**
     * @var array<string>
     */
    private $defaultVariableNames;
    /**
     * @var array<string>
     */
    private $renderMethodNames;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    /**
     * @param array<string> $defaultVariableNames
     * @param array<string> $renderMethodNames
     */
    public function __construct(array $defaultVariableNames, array $renderMethodNames, SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->defaultVariableNames = $defaultVariableNames;
        $this->renderMethodNames = $renderMethodNames;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
        $this->createdVariableNamesResolver = new CreatedVariableNamesResolver($nodeFinder, $simpleNameResolver);
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        // reset to avoid used variable name in next analysed file
        $this->userVariableNames = [];
        $this->createdVariableNames = [];
        return $nodes;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }
        if (!$this->simpleNameResolver->isNames($node, $this->renderMethodNames)) {
            return null;
        }
        $this->userVariableNames = \array_merge($this->userVariableNames, $this->resolveClassMethodVariableNames($node));
        return null;
    }
    /**
     * @return string[]
     */
    public function getUsedVariableNames() : array
    {
        $removedVariableNames = \array_merge($this->defaultVariableNames, $this->createdVariableNames);
        return \array_diff($this->userVariableNames, $removedVariableNames);
    }
    /**
     * @return string[]
     */
    private function resolveClassMethodVariableNames(ClassMethod $classMethod) : array
    {
        $variableNames = [];
        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Variable::class);
        $createdVariableNames = $this->createdVariableNamesResolver->resolve($classMethod);
        foreach ($variables as $variable) {
            $variableName = $this->simpleNameResolver->getName($variable);
            if ($variableName === null) {
                continue;
            }
            if (\in_array($variableName, $createdVariableNames, \true)) {
                continue;
            }
            $variableNames[] = $variableName;
        }
        $this->createdVariableNames = $createdVariableNames;
        return $variableNames;
    }
}

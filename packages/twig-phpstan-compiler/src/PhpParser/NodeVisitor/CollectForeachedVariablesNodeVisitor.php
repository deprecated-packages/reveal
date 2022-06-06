<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt\Foreach_;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class CollectForeachedVariablesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<string, string>
     */
    private $foreachedVariablesToSingles = [];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->foreachedVariablesToSingles = [];
        return $nodes;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Foreach_) {
            return null;
        }
        if (!$node->expr instanceof Variable) {
            return null;
        }
        if (!$node->valueVar instanceof Variable) {
            return null;
        }
        $foreachedVariable = $this->simpleNameResolver->getName($node->expr);
        if ($foreachedVariable === null) {
            return null;
        }
        $singleVariable = $this->simpleNameResolver->getName($node->valueVar);
        if ($singleVariable === null) {
            return null;
        }
        $this->foreachedVariablesToSingles[$foreachedVariable] = $singleVariable;
        return null;
    }
    /**
     * @return array<string, string>
     */
    public function getForeachedVariablesToSingles() : array
    {
        return $this->foreachedVariablesToSingles;
    }
}

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\Normalization;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayDimFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\Assign;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use RevealPrefix20220606\PhpParser\Node\Stmt\Foreach_;
use RevealPrefix20220606\PhpParser\Node\Stmt\Unset_;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\Contract\NodeVisitor\NormalizingNodeVisitorInterface;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class DoDisplayCleanupNormalizeNodeVisitor extends NodeVisitorAbstract implements NormalizingNodeVisitorInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function leaveNode(Node $node)
    {
        if ($node instanceof Expression) {
            $expr = $node->expr;
            if ($expr instanceof MethodCall) {
                if ($this->simpleNameResolver->isName($expr->name, 'displayBlock')) {
                    return NodeTraverser::REMOVE_NODE;
                }
            }
            if ($expr instanceof Assign && $this->isContextVariable($expr->expr)) {
                return NodeTraverser::REMOVE_NODE;
            }
        }
        if ($node instanceof Foreach_) {
            if ($node->keyVar instanceof ArrayDimFetch) {
                $arrayDimFetch = $node->keyVar;
                if ($this->isContextVariable($arrayDimFetch->var)) {
                    // remove $context['...'] key
                    $node->keyVar = null;
                }
                return $node;
            }
        }
        if ($node instanceof Unset_) {
            return NodeTraverser::REMOVE_NODE;
        }
        if (!$node instanceof Expression) {
            return null;
        }
        if ($node->expr instanceof Node\Expr\FuncCall) {
            $funcCall = $node->expr;
            if ($this->simpleNameResolver->isName($funcCall->name, 'extract')) {
                return NodeTraverser::REMOVE_NODE;
            }
        }
        $expr = $node->expr;
        if ($expr instanceof Assign) {
            if ($expr->var instanceof Variable) {
                $variable = $expr->var;
                if ($this->simpleNameResolver->isNames($variable, ['_parent', 'context'])) {
                    return NodeTraverser::REMOVE_NODE;
                }
            }
        }
        return null;
    }
    private function isContextVariable(Expr $expr) : bool
    {
        if (!$expr instanceof Variable) {
            return \false;
        }
        return $this->simpleNameResolver->isName($expr, 'context');
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\Normalization\\DoDisplayCleanupNormalizeNodeVisitor', 'Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\Normalization\\DoDisplayCleanupNormalizeNodeVisitor', \false);

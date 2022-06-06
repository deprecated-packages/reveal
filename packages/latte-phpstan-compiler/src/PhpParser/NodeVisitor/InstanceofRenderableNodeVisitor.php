<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\Instanceof_;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use RevealPrefix20220606\PhpParser\Node\Stmt\If_;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * Fixes render() invalid contract
 *
 * @see https://github.com/symplify/symplify/issues/3682
 */
final class InstanceofRenderableNodeVisitor extends NodeVisitorAbstract implements LatteToPhpCompilerNodeVisitorInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    /**
     * @return \PhpParser\Node|null|int
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof If_) {
            return null;
        }
        if ($node->elseifs !== []) {
            return null;
        }
        if (!$node->cond instanceof Instanceof_) {
            return null;
        }
        $instanceof = $node->cond;
        if (!$this->simpleNameResolver->isNames($instanceof->class, ['RevealPrefix20220606\\Nette\\Application\\UI\\IRenderable', 'RevealPrefix20220606\\Nette\\Application\\UI\\Renderable'])) {
            return null;
        }
        $redrawMethodCall = $this->matchRedrawControlMethodCall($node);
        if (!$redrawMethodCall instanceof MethodCall) {
            return null;
        }
        return NodeTraverser::REMOVE_NODE;
    }
    private function matchRedrawControlMethodCall(If_ $if) : ?MethodCall
    {
        if ($if->stmts === []) {
            return null;
        }
        $onlyStmt = $if->stmts[0];
        if (!$onlyStmt instanceof Expression) {
            return null;
        }
        $stmtExpr = $onlyStmt->expr;
        if (!$stmtExpr instanceof MethodCall) {
            return null;
        }
        return $stmtExpr;
    }
}
/**
 * Fixes render() invalid contract
 *
 * @see https://github.com/symplify/symplify/issues/3682
 */
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\InstanceofRenderableNodeVisitor', 'Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\InstanceofRenderableNodeVisitor', \false);

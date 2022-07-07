<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
use RevealPrefix20220707\Symplify\Astral\Naming\SimpleNameResolver;
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
        if (!$this->simpleNameResolver->isNames($instanceof->class, ['RevealPrefix20220707\\Nette\\Application\\UI\\IRenderable', 'RevealPrefix20220707\\Nette\\Application\\UI\\Renderable'])) {
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

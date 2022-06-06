<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\Assign;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PhpParser\Node\Stmt\Echo_;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use RevealPrefix20220606\PhpParser\Node\Stmt\Nop;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class ExtractDoDisplayStmtsNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var Stmt[]
     */
    private $doDisplayStmts = [];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function beforeTraverse(array $nodes)
    {
        $this->doDisplayStmts = [];
        return $nodes;
    }
    public function enterNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }
        if (!$this->simpleNameResolver->isNames($node, ['doDisplay', 'block_*'])) {
            return null;
        }
        if ($node->stmts === null) {
            return null;
        }
        foreach ($node->stmts as $stmt) {
            if ($this->isMacrosAssign($stmt)) {
                $docComment = $stmt->getDocComment();
                if ($docComment === null) {
                    continue;
                }
                // keep @var doc types
                $nop = new Nop();
                $nop->setDocComment($docComment);
                $this->doDisplayStmts[] = $nop;
                continue;
            }
            if ($stmt instanceof Expression && $stmt->expr instanceof FuncCall) {
                $funcCall = $stmt->expr;
                if ($this->simpleNameResolver->isName($funcCall, 'extract')) {
                    continue;
                }
            }
            // unwrap "echo twig_escape_filter(..., $variable);"
            // to "echo $variable;"
            if ($stmt instanceof Echo_) {
                $onlyExpr = $stmt->exprs[0];
                if ($onlyExpr instanceof FuncCall && $this->simpleNameResolver->isName($onlyExpr, 'twig_escape_filter')) {
                    $funcCall = $onlyExpr;
                    $stmt->exprs = [$funcCall->getArgs()[1]->value];
                }
            }
            $this->doDisplayStmts[] = $stmt;
        }
        return null;
    }
    /**
     * @return Stmt[]
     */
    public function getDoDisplayStmts() : array
    {
        return $this->doDisplayStmts;
    }
    private function isMacrosAssign(Stmt $stmt) : bool
    {
        if (!$stmt instanceof Expression) {
            return \false;
        }
        $expr = $stmt->expr;
        if (!$expr instanceof Assign) {
            return \false;
        }
        if (!$expr->var instanceof Variable) {
            return \false;
        }
        return $this->simpleNameResolver->isName($expr->var, 'macros');
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\ExtractDoDisplayStmtsNodeVisitor', 'Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\ExtractDoDisplayStmtsNodeVisitor', \false);

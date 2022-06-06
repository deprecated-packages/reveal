<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\Normalization;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Name;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\Contract\NodeVisitor\NormalizingNodeVisitorInterface;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class LoadParentTemplateNormalizeNodeVisitor extends NodeVisitorAbstract implements NormalizingNodeVisitorInterface
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
        if (!$node instanceof Expression) {
            return null;
        }
        $expr = $node->expr;
        if (!$expr instanceof MethodCall) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($expr->name, 'display')) {
            return null;
        }
        return NodeTraverser::REMOVE_NODE;
    }
    public function enterNode(\RevealPrefix20220606\PhpParser\Node $node)
    {
        // assign
        // load template method call
        if ($node instanceof Node\Expr\Assign) {
            if ($node->expr instanceof MethodCall) {
                $methodCall = $node->expr;
                if (!$this->simpleNameResolver->isName($methodCall->name, 'loadTemplate')) {
                    return null;
                }
                $templateString = $methodCall->getArgs()[0]->value;
                $args = [new Arg($templateString)];
                return new FuncCall(new Name('load_parent_template'), $args);
            }
        }
        return null;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\Normalization\\LoadParentTemplateNormalizeNodeVisitor', 'Reveal\\TwigPHPStanCompiler\\PhpParser\\NodeVisitor\\Normalization\\LoadParentTemplateNormalizeNodeVisitor', \false);

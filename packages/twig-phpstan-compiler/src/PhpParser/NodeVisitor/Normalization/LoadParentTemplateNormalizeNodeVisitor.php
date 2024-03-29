<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\Normalization;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Reveal\TwigPHPStanCompiler\Contract\NodeVisitor\NormalizingNodeVisitorInterface;
use Symplify\Astral\Naming\SimpleNameResolver;
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
    public function enterNode(\PhpParser\Node $node)
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

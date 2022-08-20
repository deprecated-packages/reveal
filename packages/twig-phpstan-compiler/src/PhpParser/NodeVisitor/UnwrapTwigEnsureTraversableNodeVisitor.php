<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
final class UnwrapTwigEnsureTraversableNodeVisitor extends NodeVisitorAbstract
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
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof FuncCall) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($node, 'twig_ensure_traversable')) {
            return null;
        }
        $firstArg = $node->getArgs()[0];
        return $firstArg->value;
    }
}

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\NodeNameResolver;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\Symplify\Astral\Contract\NodeNameResolverInterface;
final class FuncCallNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof FuncCall;
    }
    /**
     * @param FuncCall $node
     */
    public function resolve(Node $node) : ?string
    {
        if ($node->name instanceof Expr) {
            return null;
        }
        return (string) $node->name;
    }
}

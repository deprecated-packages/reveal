<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayDimFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp\Coalesce;
use RevealPrefix20220606\PhpParser\Node\Expr\ConstFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class UnwrapCoalesceContextNodeVisitor extends NodeVisitorAbstract
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
        if (!$node instanceof Coalesce) {
            return null;
        }
        // only variable and array dim fetches
        if (!$node->left instanceof Variable && !$node->left instanceof ArrayDimFetch) {
            return null;
        }
        if (!$node->right instanceof ConstFetch) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($node->right, 'null')) {
            return null;
        }
        return $node->left;
    }
}

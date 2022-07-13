<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use RevealPrefix20220713\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * Turns: $context['value'] ↓ $value
 */
final class UnwrapContextVariableNodeVisitor extends NodeVisitorAbstract
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
        if (!$node instanceof ArrayDimFetch) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($node->var, 'context')) {
            return null;
        }
        if (!$node->dim instanceof String_) {
            return null;
        }
        $string = $node->dim;
        $stringValue = $string->value;
        // meta variable → skip
        if (\strncmp($stringValue, '_', \strlen('_')) === 0) {
            return null;
        }
        return new Variable($stringValue);
    }
}

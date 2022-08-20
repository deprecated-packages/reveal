<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RevealPrefix20220820\Twig\Node\Expression;

use RevealPrefix20220820\Twig\Compiler;
use RevealPrefix20220820\Twig\Node\Node;
/**
 * @internal
 */
final class InlinePrint extends AbstractExpression
{
    public function __construct(Node $node, int $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('print (')->subcompile($this->getNode('node'))->raw(')');
    }
}

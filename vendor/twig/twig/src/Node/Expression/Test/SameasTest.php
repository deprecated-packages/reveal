<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RevealPrefix20220606\Twig\Node\Expression\Test;

use RevealPrefix20220606\Twig\Compiler;
use RevealPrefix20220606\Twig\Node\Expression\TestExpression;
/**
 * Checks if a variable is the same as another one (=== in PHP).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SameasTest extends TestExpression
{
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('(')->subcompile($this->getNode('node'))->raw(' === ')->subcompile($this->getNode('arguments')->getNode(0))->raw(')');
    }
}

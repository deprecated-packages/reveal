<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RevealPrefix20220820\Twig\Node\Expression\Test;

use RevealPrefix20220820\Twig\Compiler;
use RevealPrefix20220820\Twig\Node\Expression\TestExpression;
/**
 * Checks that a variable is null.
 *
 *  {{ var is none }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NullTest extends TestExpression
{
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('(null === ')->subcompile($this->getNode('node'))->raw(')');
    }
}

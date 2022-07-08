<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RevealPrefix20220708\Twig\Node\Expression\Binary;

use RevealPrefix20220708\Twig\Compiler;
class ModBinary extends AbstractBinary
{
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('%');
    }
}

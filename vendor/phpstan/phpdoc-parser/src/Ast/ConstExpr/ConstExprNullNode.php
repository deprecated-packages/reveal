<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PHPStan\PhpDocParser\Ast\ConstExpr;

use RevealPrefix20220606\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprNullNode implements ConstExprNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return 'null';
    }
}

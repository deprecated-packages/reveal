<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;

use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;
class ShiftLeft extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '<<';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_ShiftLeft';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\BinaryOp\\ShiftLeft', 'PhpParser\\Node\\Expr\\BinaryOp\\ShiftLeft', \false);

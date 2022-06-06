<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;

use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;
class Mul extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '*';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Mul';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\BinaryOp\\Mul', 'PhpParser\\Node\\Expr\\BinaryOp\\Mul', \false);

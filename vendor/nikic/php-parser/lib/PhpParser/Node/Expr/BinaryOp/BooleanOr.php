<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;

use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;
class BooleanOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '||';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_BooleanOr';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\BinaryOp\\BooleanOr', 'PhpParser\\Node\\Expr\\BinaryOp\\BooleanOr', \false);

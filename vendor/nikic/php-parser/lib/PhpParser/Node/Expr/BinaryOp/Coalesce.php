<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;

use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp;
class Coalesce extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '??';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Coalesce';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\BinaryOp\\Coalesce', 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce', \false);

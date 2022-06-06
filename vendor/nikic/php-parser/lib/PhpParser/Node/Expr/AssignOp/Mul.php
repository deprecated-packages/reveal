<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class Mul extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Mul';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\Mul', 'PhpParser\\Node\\Expr\\AssignOp\\Mul', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class Concat extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Concat';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\Concat', 'PhpParser\\Node\\Expr\\AssignOp\\Concat', \false);

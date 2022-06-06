<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\ShiftRight', 'PhpParser\\Node\\Expr\\AssignOp\\ShiftRight', \false);

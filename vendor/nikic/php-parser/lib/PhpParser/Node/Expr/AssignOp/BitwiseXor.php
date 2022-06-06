<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class BitwiseXor extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseXor';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\BitwiseXor', 'PhpParser\\Node\\Expr\\AssignOp\\BitwiseXor', \false);

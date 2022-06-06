<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Plus';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\Plus', 'PhpParser\\Node\\Expr\\AssignOp\\Plus', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;

use RevealPrefix20220606\PhpParser\Node\Expr\AssignOp;
class Div extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Div';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\AssignOp\\Div', 'PhpParser\\Node\\Expr\\AssignOp\\Div', \false);

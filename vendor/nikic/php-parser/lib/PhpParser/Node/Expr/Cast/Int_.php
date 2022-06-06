<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\Cast;

use RevealPrefix20220606\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast\\Int_', 'PhpParser\\Node\\Expr\\Cast\\Int_', \false);

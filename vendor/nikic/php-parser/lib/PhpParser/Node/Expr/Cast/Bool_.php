<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\Cast;

use RevealPrefix20220606\PhpParser\Node\Expr\Cast;
class Bool_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Bool';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast\\Bool_', 'PhpParser\\Node\\Expr\\Cast\\Bool_', \false);

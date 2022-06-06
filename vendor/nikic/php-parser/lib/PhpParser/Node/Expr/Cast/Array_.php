<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\Cast;

use RevealPrefix20220606\PhpParser\Node\Expr\Cast;
class Array_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast\\Array_', 'PhpParser\\Node\\Expr\\Cast\\Array_', \false);

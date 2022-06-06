<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\Cast;

use RevealPrefix20220606\PhpParser\Node\Expr\Cast;
class Object_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast\\Object_', 'PhpParser\\Node\\Expr\\Cast\\Object_', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr\Cast;

use RevealPrefix20220606\PhpParser\Node\Expr\Cast;
class Unset_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast\\Unset_', 'PhpParser\\Node\\Expr\\Cast\\Unset_', \false);

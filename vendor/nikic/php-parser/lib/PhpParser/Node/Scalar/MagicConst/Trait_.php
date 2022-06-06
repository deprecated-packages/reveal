<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;

use RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;
class Trait_ extends MagicConst
{
    public function getName() : string
    {
        return '__TRAIT__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Trait';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Scalar\\MagicConst\\Trait_', 'PhpParser\\Node\\Scalar\\MagicConst\\Trait_', \false);

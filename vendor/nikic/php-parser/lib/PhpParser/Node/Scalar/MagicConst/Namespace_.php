<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;

use RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;
class Namespace_ extends MagicConst
{
    public function getName() : string
    {
        return '__NAMESPACE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Namespace';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Scalar\\MagicConst\\Namespace_', 'PhpParser\\Node\\Scalar\\MagicConst\\Namespace_', \false);

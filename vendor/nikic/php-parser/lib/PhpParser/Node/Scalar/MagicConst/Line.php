<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;

use RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;
class Line extends MagicConst
{
    public function getName() : string
    {
        return '__LINE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Line';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Scalar\\MagicConst\\Line', 'PhpParser\\Node\\Scalar\\MagicConst\\Line', \false);

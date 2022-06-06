<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;

use RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;
class Method extends MagicConst
{
    public function getName() : string
    {
        return '__METHOD__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Method';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Scalar\\MagicConst\\Method', 'PhpParser\\Node\\Scalar\\MagicConst\\Method', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;

use RevealPrefix20220606\PhpParser\Node\Scalar\MagicConst;
class Dir extends MagicConst
{
    public function getName() : string
    {
        return '__DIR__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Dir';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Scalar\\MagicConst\\Dir', 'PhpParser\\Node\\Scalar\\MagicConst\\Dir', \false);

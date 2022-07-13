<?php

declare (strict_types=1);
namespace RevealPrefix20220713\Symplify\PackageBuilder\Console\Input;

use RevealPrefix20220713\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}

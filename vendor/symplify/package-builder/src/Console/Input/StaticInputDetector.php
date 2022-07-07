<?php

declare (strict_types=1);
namespace RevealPrefix20220707\Symplify\PackageBuilder\Console\Input;

use RevealPrefix20220707\Symfony\Component\Console\Input\ArgvInput;
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

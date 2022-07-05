<?php

declare (strict_types=1);
namespace RevealPrefix20220705\Symplify\PackageBuilder\Console\Input;

use RevealPrefix20220705\Symfony\Component\Console\Input\ArgvInput;
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

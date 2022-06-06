<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\ErrorHandler;

use RevealPrefix20220606\PhpParser\Error;
use RevealPrefix20220606\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
\class_alias('RevealPrefix20220606\\PhpParser\\ErrorHandler\\Throwing', 'PhpParser\\ErrorHandler\\Throwing', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606;

use RevealPrefix20220606\Reveal\Kernel\RevealKernel;
use RevealPrefix20220606\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
require __DIR__ . '/../vendor/autoload.php';
$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(RevealKernel::class);
$kernelBootAndApplicationRun->run();

<?php

declare (strict_types=1);
namespace RevealPrefix20220713\Symplify\SymplifyKernel\Contract\Config;

use RevealPrefix20220713\Symfony\Component\Config\Loader\LoaderInterface;
use RevealPrefix20220713\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}

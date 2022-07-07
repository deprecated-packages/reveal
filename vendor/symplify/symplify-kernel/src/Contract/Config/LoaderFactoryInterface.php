<?php

declare (strict_types=1);
namespace RevealPrefix20220707\Symplify\SymplifyKernel\Contract\Config;

use RevealPrefix20220707\Symfony\Component\Config\Loader\LoaderInterface;
use RevealPrefix20220707\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}

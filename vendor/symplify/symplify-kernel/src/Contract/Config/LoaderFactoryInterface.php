<?php

declare (strict_types=1);
namespace RevealPrefix20220820\Symplify\SymplifyKernel\Contract\Config;

use RevealPrefix20220820\Symfony\Component\Config\Loader\LoaderInterface;
use RevealPrefix20220820\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}

<?php

declare (strict_types=1);
namespace RevealPrefix20220708\Symplify\SymplifyKernel\Contract\Config;

use RevealPrefix20220708\Symfony\Component\Config\Loader\LoaderInterface;
use RevealPrefix20220708\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}

<?php

declare (strict_types=1);
namespace RevealPrefix20220705\Symplify\SymplifyKernel\Contract\Config;

use RevealPrefix20220705\Symfony\Component\Config\Loader\LoaderInterface;
use RevealPrefix20220705\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}

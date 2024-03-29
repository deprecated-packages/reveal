<?php

declare (strict_types=1);
namespace RevealPrefix20220820\Symplify\SymplifyKernel\Config\Loader;

use RevealPrefix20220820\Symfony\Component\Config\FileLocator;
use RevealPrefix20220820\Symfony\Component\Config\Loader\DelegatingLoader;
use RevealPrefix20220820\Symfony\Component\Config\Loader\GlobFileLoader;
use RevealPrefix20220820\Symfony\Component\Config\Loader\LoaderResolver;
use RevealPrefix20220820\Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use RevealPrefix20220820\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \RevealPrefix20220820\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new FileLocator([$currentWorkingDirectory]);
        $loaders = [new GlobFileLoader($fileLocator), new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new LoaderResolver($loaders);
        return new DelegatingLoader($loaderResolver);
    }
}

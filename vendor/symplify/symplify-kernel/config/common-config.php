<?php

declare (strict_types=1);
namespace RevealPrefix20220711;

use RevealPrefix20220711\Symfony\Component\Console\Style\SymfonyStyle;
use RevealPrefix20220711\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220711\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use RevealPrefix20220711\Symplify\PackageBuilder\Parameter\ParameterProvider;
use RevealPrefix20220711\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use RevealPrefix20220711\Symplify\SmartFileSystem\FileSystemFilter;
use RevealPrefix20220711\Symplify\SmartFileSystem\FileSystemGuard;
use RevealPrefix20220711\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use RevealPrefix20220711\Symplify\SmartFileSystem\Finder\SmartFinder;
use RevealPrefix20220711\Symplify\SmartFileSystem\SmartFileSystem;
use function RevealPrefix20220711\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    // symfony style
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemGuard::class);
    $services->set(FileSystemFilter::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(PrivatesAccessor::class);
};

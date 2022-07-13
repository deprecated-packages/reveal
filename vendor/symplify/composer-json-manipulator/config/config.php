<?php

declare (strict_types=1);
namespace RevealPrefix20220713;

use RevealPrefix20220713\Symfony\Component\Console\Style\SymfonyStyle;
use RevealPrefix20220713\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220713\Symplify\ComposerJsonManipulator\ValueObject\Option;
use RevealPrefix20220713\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use RevealPrefix20220713\Symplify\PackageBuilder\Parameter\ParameterProvider;
use RevealPrefix20220713\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use RevealPrefix20220713\Symplify\SmartFileSystem\SmartFileSystem;
use function RevealPrefix20220713\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('RevealPrefix20220713\Symplify\\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};

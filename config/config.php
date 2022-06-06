<?php

declare (strict_types=1);
namespace RevealPrefix20220606;

use Reveal\Console\ApplicationFactory;
use RevealPrefix20220606\Symfony\Component\Console\Application;
use RevealPrefix20220606\Symfony\Component\Console\Style\SymfonyStyle;
use RevealPrefix20220606\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220606\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use function RevealPrefix20220606\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/../packages/twig-phpstan-compiler/config/services.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autoconfigure()->autowire();
    $services->load('Reveal\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/Enum']);
    $services->set(Application::class)->factory([service(ApplicationFactory::class), 'create']);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};

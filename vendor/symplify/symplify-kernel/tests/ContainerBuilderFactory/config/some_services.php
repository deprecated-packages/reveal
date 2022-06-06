<?php

declare (strict_types=1);
namespace RevealPrefix20220606;

use RevealPrefix20220606\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220606\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};

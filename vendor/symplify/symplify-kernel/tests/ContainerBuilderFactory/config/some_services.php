<?php

declare (strict_types=1);
namespace RevealPrefix20220708;

use RevealPrefix20220708\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220708\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};

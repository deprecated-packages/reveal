<?php

declare (strict_types=1);
namespace RevealPrefix20220820;

use RevealPrefix20220820\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};

<?php

declare (strict_types=1);
namespace RevealPrefix20220606;

use RevealPrefix20220606\SebastianBergmann\Diff\Differ;
use RevealPrefix20220606\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220606\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use RevealPrefix20220606\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use RevealPrefix20220606\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use RevealPrefix20220606\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};

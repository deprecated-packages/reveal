<?php

declare (strict_types=1);
namespace RevealPrefix20220707;

use RevealPrefix20220707\SebastianBergmann\Diff\Differ;
use RevealPrefix20220707\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use RevealPrefix20220707\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use RevealPrefix20220707\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use RevealPrefix20220707\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use RevealPrefix20220707\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};

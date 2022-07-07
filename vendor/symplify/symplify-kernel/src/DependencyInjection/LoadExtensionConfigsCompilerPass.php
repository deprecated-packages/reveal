<?php

declare (strict_types=1);
namespace RevealPrefix20220707\Symplify\SymplifyKernel\DependencyInjection;

use RevealPrefix20220707\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use RevealPrefix20220707\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Mimics @see \Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass without dependency on
 * symfony/http-kernel
 */
final class LoadExtensionConfigsCompilerPass extends MergeExtensionConfigurationPass
{
    public function process(ContainerBuilder $containerBuilder) : void
    {
        $extensionNames = \array_keys($containerBuilder->getExtensions());
        foreach ($extensionNames as $extensionName) {
            $containerBuilder->loadFromExtension($extensionName, []);
        }
        parent::process($containerBuilder);
    }
}

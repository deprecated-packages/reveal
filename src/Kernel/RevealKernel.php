<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\Kernel;

use RevealPrefix20220606\Psr\Container\ContainerInterface;
use RevealPrefix20220606\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use RevealPrefix20220606\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class RevealKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = [new AutowireArrayParameterCompilerPass()];
        return $this->create($configFiles, $compilerPasses, []);
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\Kernel\\RevealKernel', 'Reveal\\Kernel\\RevealKernel', \false);

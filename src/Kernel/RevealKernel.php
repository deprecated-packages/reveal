<?php

declare (strict_types=1);
namespace Reveal\Kernel;

use RevealPrefix20220820\Psr\Container\ContainerInterface;
use RevealPrefix20220820\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use RevealPrefix20220820\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class RevealKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $compilerPasses = [new AutowireArrayParameterCompilerPass()];
        return $this->create($configFiles, $compilerPasses, []);
    }
}

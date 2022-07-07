<?php

declare (strict_types=1);
namespace RevealPrefix20220707\Symplify\EasyTesting\Kernel;

use RevealPrefix20220707\Psr\Container\ContainerInterface;
use RevealPrefix20220707\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use RevealPrefix20220707\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}

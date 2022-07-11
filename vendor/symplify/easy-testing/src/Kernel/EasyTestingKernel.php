<?php

declare (strict_types=1);
namespace RevealPrefix20220711\Symplify\EasyTesting\Kernel;

use RevealPrefix20220711\Psr\Container\ContainerInterface;
use RevealPrefix20220711\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use RevealPrefix20220711\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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

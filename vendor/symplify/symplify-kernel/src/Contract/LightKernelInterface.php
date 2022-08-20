<?php

declare (strict_types=1);
namespace RevealPrefix20220820\Symplify\SymplifyKernel\Contract;

use RevealPrefix20220820\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface;
    public function getContainer() : ContainerInterface;
}

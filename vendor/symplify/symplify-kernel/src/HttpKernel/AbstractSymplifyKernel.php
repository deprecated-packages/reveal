<?php

declare (strict_types=1);
namespace RevealPrefix20220708\Symplify\SymplifyKernel\HttpKernel;

use RevealPrefix20220708\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use RevealPrefix20220708\Symfony\Component\DependencyInjection\Container;
use RevealPrefix20220708\Symfony\Component\DependencyInjection\ContainerInterface;
use RevealPrefix20220708\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use RevealPrefix20220708\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use RevealPrefix20220708\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use RevealPrefix20220708\Symplify\SymplifyKernel\ContainerBuilderFactory;
use RevealPrefix20220708\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use RevealPrefix20220708\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use RevealPrefix20220708\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []) : ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $compilerPasses[] = new AutowireArrayParameterCompilerPass();
        $configFiles[] = SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \RevealPrefix20220708\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }
        return $this->container;
    }
}

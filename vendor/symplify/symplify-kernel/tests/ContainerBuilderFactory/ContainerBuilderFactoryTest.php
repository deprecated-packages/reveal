<?php

declare (strict_types=1);
namespace RevealPrefix20220711\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use RevealPrefix20220711\PHPUnit\Framework\TestCase;
use RevealPrefix20220711\Symplify\SmartFileSystem\SmartFileSystem;
use RevealPrefix20220711\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use RevealPrefix20220711\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}

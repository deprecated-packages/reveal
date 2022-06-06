<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\PHPStan;

use RevealPrefix20220606\PHPStan\Analyser\FileAnalyser;
use RevealPrefix20220606\PHPStan\DependencyInjection\Container;
use RevealPrefix20220606\PHPStan\DependencyInjection\ContainerFactory;
use RevealPrefix20220606\PHPStan\Parser\Parser;
/**
 * @see https://github.com/rectorphp/rector-src/blob/main/packages/NodeTypeResolver/DependencyInjection/PHPStanServicesFactory.php
 */
final class PHPStanServicesFactory
{
    /**
     * @readonly
     * @var \PHPStan\DependencyInjection\Container
     */
    private $container;
    public function __construct()
    {
        $containerFactory = new ContainerFactory(\getcwd());
        $additionalConfigs = [__DIR__ . '/../../config/php-parser.neon'];
        $this->container = $containerFactory->create(\sys_get_temp_dir(), $additionalConfigs, []);
    }
    public function createFileAnalyser() : FileAnalyser
    {
        return $this->container->getByType(FileAnalyser::class);
    }
    public function createPHPStanParser() : Parser
    {
        return $this->container->getService('currentPhpVersionRichParser');
    }
}

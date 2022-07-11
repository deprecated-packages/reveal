<?php

declare (strict_types=1);
namespace RevealPrefix20220711;

use PHPStan\Analyser\FileAnalyser;
use PHPStan\Parser\Parser;
use Reveal\TemplatePHPStanCompiler\PHPStan\PHPStanServicesFactory;
use RevealPrefix20220711\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function RevealPrefix20220711\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/../../../vendor/symplify/astral/config/config.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('Reveal\\TemplatePHPStanCompiler\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/NodeVisitor', __DIR__ . '/../src/Rules/TemplateRulesRegistry.php', __DIR__ . '/../src/ValueObject']);
    // phpstan services
    $services->set(FileAnalyser::class)->factory([service(PHPStanServicesFactory::class), 'createFileAnalyser']);
    $services->set(Parser::class)->factory([service(PHPStanServicesFactory::class), 'createPHPStanParser']);
};

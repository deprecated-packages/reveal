<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\LatteVariableNamesResolver;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver;
/**
 * @api
 */
final class UnusedNetteTemplateRenderVariableResolver
{
    /**
     * @var \Reveal\LattePHPStanCompiler\LatteVariableNamesResolver
     */
    private $latteVariableNamesResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver
     */
    private $methodCallArrayResolver;
    public function __construct(LatteVariableNamesResolver $latteVariableNamesResolver, MethodCallArrayResolver $methodCallArrayResolver)
    {
        $this->latteVariableNamesResolver = $latteVariableNamesResolver;
        $this->methodCallArrayResolver = $methodCallArrayResolver;
    }
    /**
     * @return string[]
     */
    public function resolveMethodCallAndTemplate(MethodCall $methodCall, string $templateFilePath, Scope $scope) : array
    {
        $templateUsedVariableNames = $this->latteVariableNamesResolver->resolveFromFilePath($templateFilePath);
        $passedVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition($methodCall, $scope, 1);
        return \array_diff($passedVariableNames, $templateUsedVariableNames);
    }
}
/**
 * @api
 */
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\NodeAnalyzer\\UnusedNetteTemplateRenderVariableResolver', 'Reveal\\LattePHPStanCompiler\\NodeAnalyzer\\UnusedNetteTemplateRenderVariableResolver', \false);

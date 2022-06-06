<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Reveal\LattePHPStanCompiler\LatteVariableNamesResolver;
use Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver;
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

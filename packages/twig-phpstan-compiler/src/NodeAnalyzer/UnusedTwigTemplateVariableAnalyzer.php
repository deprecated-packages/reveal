<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver;
/**
 * @api
 */
final class UnusedTwigTemplateVariableAnalyzer
{
    /**
     * @var \Reveal\TwigPHPStanCompiler\NodeAnalyzer\TwigVariableNamesResolver
     */
    private $twigVariableNamesResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver
     */
    private $methodCallArrayResolver;
    public function __construct(\Reveal\TwigPHPStanCompiler\NodeAnalyzer\TwigVariableNamesResolver $twigVariableNamesResolver, MethodCallArrayResolver $methodCallArrayResolver)
    {
        $this->twigVariableNamesResolver = $twigVariableNamesResolver;
        $this->methodCallArrayResolver = $methodCallArrayResolver;
    }
    /**
     * @param string[] $templateFilePaths
     * @return string[]
     */
    public function resolveMethodCallAndTemplate(MethodCall $methodCall, array $templateFilePaths, Scope $scope) : array
    {
        if ($templateFilePaths === []) {
            return [];
        }
        $templatesUsedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $currentUsedVariableNames = $this->twigVariableNamesResolver->resolveFromFilePath($templateFilePath);
            $templatesUsedVariableNames = \array_merge($templatesUsedVariableNames, $currentUsedVariableNames);
        }
        $passedVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition($methodCall, $scope, 1);
        return \array_diff($passedVariableNames, $templatesUsedVariableNames);
    }
}

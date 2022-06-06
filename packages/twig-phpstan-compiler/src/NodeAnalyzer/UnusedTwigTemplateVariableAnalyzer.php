<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver;
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
    public function __construct(TwigVariableNamesResolver $twigVariableNamesResolver, MethodCallArrayResolver $methodCallArrayResolver)
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
/**
 * @api
 */
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\NodeAnalyzer\\UnusedTwigTemplateVariableAnalyzer', 'Reveal\\TwigPHPStanCompiler\\NodeAnalyzer\\UnusedTwigTemplateVariableAnalyzer', \false);

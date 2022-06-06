<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\NodeAnalyzer;

use PHPStan\Analyser\Scope;
use Reveal\TemplatePHPStanCompiler\NodeAnalyzer\ParametersArrayAnalyzer;
use Reveal\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
/**
 * @api
 */
final class MissingTwigTemplateRenderVariableResolver
{
    /**
     * @var \Reveal\TwigPHPStanCompiler\NodeAnalyzer\TwigVariableNamesResolver
     */
    private $twigVariableNamesResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeAnalyzer\ParametersArrayAnalyzer
     */
    private $parametersArrayAnalyzer;
    public function __construct(\Reveal\TwigPHPStanCompiler\NodeAnalyzer\TwigVariableNamesResolver $twigVariableNamesResolver, ParametersArrayAnalyzer $parametersArrayAnalyzer)
    {
        $this->twigVariableNamesResolver = $twigVariableNamesResolver;
        $this->parametersArrayAnalyzer = $parametersArrayAnalyzer;
    }
    /**
     * @return string[]
     */
    public function resolveFromTemplateAndMethodCall(RenderTemplateWithParameters $renderTemplateWithParameters, Scope $scope) : array
    {
        $templateUsedVariableNames = $this->twigVariableNamesResolver->resolveFromFilePath($renderTemplateWithParameters->getTemplateFilePath());
        $availableVariableNames = $this->parametersArrayAnalyzer->resolveStringKeys($renderTemplateWithParameters->getParametersArray(), $scope);
        // default variables
        $availableVariableNames[] = 'app';
        $availableVariableNames[] = 'blocks';
        $missingVariableNames = \array_diff($templateUsedVariableNames, $availableVariableNames);
        return \array_unique($missingVariableNames);
    }
}

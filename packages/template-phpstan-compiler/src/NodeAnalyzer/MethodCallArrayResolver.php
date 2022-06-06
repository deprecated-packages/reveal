<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
/**
 * @api
 */
final class MethodCallArrayResolver
{
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeAnalyzer\ParametersArrayAnalyzer
     */
    private $parametersArrayAnalyzer;
    public function __construct(ParametersArrayAnalyzer $parametersArrayAnalyzer)
    {
        $this->parametersArrayAnalyzer = $parametersArrayAnalyzer;
    }
    /**
     * @return string[]
     */
    public function resolveArrayKeysOnPosition(MethodCall $methodCall, Scope $scope, int $position) : array
    {
        if (!isset($methodCall->args[$position])) {
            return [];
        }
        $argOrVariadicPlaceholder = $methodCall->args[$position];
        if (!$argOrVariadicPlaceholder instanceof Arg) {
            return [];
        }
        $secondArgValue = $argOrVariadicPlaceholder->value;
        if (!$secondArgValue instanceof Array_) {
            return [];
        }
        return $this->parametersArrayAnalyzer->resolveStringKeys($secondArgValue, $scope);
    }
}

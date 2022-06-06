<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Symplify\Astral\NodeValue\NodeValueResolver;
final class ParametersArrayAnalyzer
{
    /**
     * @var \Symplify\Astral\NodeValue\NodeValueResolver
     */
    private $nodeValueResolver;
    public function __construct(NodeValueResolver $nodeValueResolver)
    {
        $this->nodeValueResolver = $nodeValueResolver;
    }
    /**
     * @return string[]
     */
    public function resolveStringKeys(Array_ $array, Scope $scope) : array
    {
        $stringKeyNames = [];
        foreach ($array->items as $arrayItem) {
            if (!$arrayItem instanceof ArrayItem) {
                continue;
            }
            if ($arrayItem->key === null) {
                continue;
            }
            $keyValue = $this->nodeValueResolver->resolve($arrayItem->key, $scope->getFile());
            if (!\is_string($keyValue)) {
                continue;
            }
            $stringKeyNames[] = $keyValue;
        }
        return $stringKeyNames;
    }
}

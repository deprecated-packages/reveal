<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\TypeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\PHPStan\Type\ArrayType;
use RevealPrefix20220606\PHPStan\Type\Generic\GenericObjectType;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220606\Symplify\Astral\NodeValue\NodeValueResolver;
/**
 * @api
 */
final class TemplateVariableTypesResolver
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
     * @return VariableAndType[]
     */
    public function resolveArray(Array_ $array, Scope $scope) : array
    {
        $variableNamesToTypes = [];
        foreach ($array->items as $arrayItem) {
            if (!$arrayItem instanceof ArrayItem) {
                continue;
            }
            if ($arrayItem->key === null) {
                continue;
            }
            $keyName = $this->nodeValueResolver->resolve($arrayItem->key, $scope->getFile());
            if (!\is_string($keyName)) {
                continue;
            }
            $variableType = $scope->getType($arrayItem->value);
            // unwrap generic object type
            if ($variableType instanceof GenericObjectType && isset($variableType->getTypes()[1])) {
                $variableType = new ArrayType($variableType->getTypes()[0], $variableType->getTypes()[1]);
            }
            $variableNamesToTypes[] = new VariableAndType($keyName, $variableType);
        }
        return $variableNamesToTypes;
    }
}

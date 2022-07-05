<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\TypeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Generic\GenericObjectType;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220705\Symplify\Astral\NodeValue\NodeValueResolver;
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

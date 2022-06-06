<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\NodeValue;

use RevealPrefix20220606\PHPStan\Type\ConstantScalarType;
use RevealPrefix20220606\PHPStan\Type\UnionType;
final class UnionTypeValueResolver
{
    /**
     * @return mixed[]
     */
    public function resolveConstantTypes(UnionType $unionType) : array
    {
        $resolvedValues = [];
        foreach ($unionType->getTypes() as $unionedType) {
            if (!$unionedType instanceof ConstantScalarType) {
                continue;
            }
            $resolvedValues[] = $unionedType->getValue();
        }
        return $resolvedValues;
    }
}

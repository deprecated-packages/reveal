<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\RuleDocGenerator\Contract\Category;

use RevealPrefix20220606\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}

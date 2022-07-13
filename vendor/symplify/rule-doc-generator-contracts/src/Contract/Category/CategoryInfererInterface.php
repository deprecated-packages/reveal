<?php

declare (strict_types=1);
namespace RevealPrefix20220713\Symplify\RuleDocGenerator\Contract\Category;

use RevealPrefix20220713\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}

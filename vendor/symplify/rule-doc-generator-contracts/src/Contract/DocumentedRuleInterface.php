<?php

declare (strict_types=1);
namespace RevealPrefix20220711\Symplify\RuleDocGenerator\Contract;

use RevealPrefix20220711\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}

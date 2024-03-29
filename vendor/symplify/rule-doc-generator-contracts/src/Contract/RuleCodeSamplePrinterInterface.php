<?php

declare (strict_types=1);
namespace RevealPrefix20220820\Symplify\RuleDocGenerator\Contract;

use RevealPrefix20220820\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class) : bool;
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition) : array;
}

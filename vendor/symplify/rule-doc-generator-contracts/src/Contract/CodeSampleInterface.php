<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    public function getGoodCode() : string;
    public function getBadCode() : string;
}

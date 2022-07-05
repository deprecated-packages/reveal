<?php

declare (strict_types=1);
namespace RevealPrefix20220705\Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    public function getGoodCode() : string;
    public function getBadCode() : string;
}

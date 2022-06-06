<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract;

use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
interface LatteVariableCollectorInterface
{
    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes() : array;
}

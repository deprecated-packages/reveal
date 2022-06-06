<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\Contract;

/**
 * @api
 */
interface UsedVariableNamesResolverInterface
{
    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath) : array;
}

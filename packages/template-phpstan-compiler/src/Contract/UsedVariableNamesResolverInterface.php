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
/**
 * @api
 */
\class_alias('RevealPrefix20220606\\Reveal\\TemplatePHPStanCompiler\\Contract\\UsedVariableNamesResolverInterface', 'Reveal\\TemplatePHPStanCompiler\\Contract\\UsedVariableNamesResolverInterface', \false);

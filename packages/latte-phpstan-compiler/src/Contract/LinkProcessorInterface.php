<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract;

use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
interface LinkProcessorInterface
{
    /**
     * checks if processor is available for target name
     */
    public function check(string $targetName) : bool;
    /**
     * @param Arg[] $linkParams
     * @param array<string, mixed> $attributes
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes) : array;
}

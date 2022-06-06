<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject;

use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface;
final class FunctionCallReference implements CallReferenceInterface
{
    /**
     * @var string
     */
    private $function;
    public function __construct(string $function)
    {
        $this->function = $function;
    }
    public function getFunction() : string
    {
        return $this->function;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\ValueObject\\FunctionCallReference', 'Reveal\\LattePHPStanCompiler\\ValueObject\\FunctionCallReference', \false);

<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\ValueObject;

use Reveal\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface;
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

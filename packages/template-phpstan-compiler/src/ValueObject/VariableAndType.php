<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject;

use RevealPrefix20220606\PHPStan\Type\Type;
use RevealPrefix20220606\PHPStan\Type\VerbosityLevel;
final class VariableAndType
{
    /**
     * @var string
     */
    private $variable;
    /**
     * @var \PHPStan\Type\Type
     */
    private $type;
    public function __construct(string $variable, Type $type)
    {
        $this->variable = $variable;
        $this->type = $type;
    }
    public function getVariable() : string
    {
        return $this->variable;
    }
    public function getType() : Type
    {
        return $this->type;
    }
    public function getTypeAsString() : string
    {
        return $this->type->describe(VerbosityLevel::typeOnly());
    }
}

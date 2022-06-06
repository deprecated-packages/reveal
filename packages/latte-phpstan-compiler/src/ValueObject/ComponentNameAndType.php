<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject;

use RevealPrefix20220606\PHPStan\Type\Type;
final class ComponentNameAndType
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var \PHPStan\Type\Type
     */
    private $returnType;
    public function __construct(string $name, Type $returnType)
    {
        $this->name = $name;
        $this->returnType = $returnType;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getReturnType() : Type
    {
        return $this->returnType;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\ValueObject\\ComponentNameAndType', 'Reveal\\LattePHPStanCompiler\\ValueObject\\ComponentNameAndType', \false);

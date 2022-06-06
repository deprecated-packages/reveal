<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\ValueObject;

use PHPStan\Type\Type;
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

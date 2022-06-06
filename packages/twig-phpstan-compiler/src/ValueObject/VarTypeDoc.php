<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\ValueObject;

final class VarTypeDoc
{
    /**
     * @var string
     */
    private $variableName;
    /**
     * @var string
     */
    private $type;
    public function __construct(string $variableName, string $type)
    {
        $this->variableName = $variableName;
        $this->type = $type;
    }
    public function getVariableName() : string
    {
        return $this->variableName;
    }
    public function getType() : string
    {
        return $this->type;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\ValueObject\\VarTypeDoc', 'Reveal\\TwigPHPStanCompiler\\ValueObject\\VarTypeDoc', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr;

use RevealPrefix20220606\PhpParser\Node\Expr;
class PreInc extends Expr
{
    /** @var Expr Variable */
    public $var;
    /**
     * Constructs a pre increment node.
     *
     * @param Expr  $var        Variable
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
    }
    public function getSubNodeNames() : array
    {
        return ['var'];
    }
    public function getType() : string
    {
        return 'Expr_PreInc';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\PreInc', 'PhpParser\\Node\\Expr\\PreInc', \false);

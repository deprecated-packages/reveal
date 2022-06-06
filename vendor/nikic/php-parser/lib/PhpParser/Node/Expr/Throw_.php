<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr;

use RevealPrefix20220606\PhpParser\Node;
class Throw_ extends Node\Expr
{
    /** @var Node\Expr Expression */
    public $expr;
    /**
     * Constructs a throw expression node.
     *
     * @param Node\Expr $expr       Expression
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }
    public function getSubNodeNames() : array
    {
        return ['expr'];
    }
    public function getType() : string
    {
        return 'Expr_Throw';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Throw_', 'PhpParser\\Node\\Expr\\Throw_', \false);

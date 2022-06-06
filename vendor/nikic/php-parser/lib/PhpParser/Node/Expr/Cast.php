<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr;

use RevealPrefix20220606\PhpParser\Node\Expr;
abstract class Cast extends Expr
{
    /** @var Expr Expression */
    public $expr;
    /**
     * Constructs a cast node.
     *
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }
    public function getSubNodeNames() : array
    {
        return ['expr'];
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Cast', 'PhpParser\\Node\\Expr\\Cast', \false);

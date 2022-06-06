<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Expr;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\MatchArm;
class Match_ extends Node\Expr
{
    /** @var Node\Expr */
    public $cond;
    /** @var MatchArm[] */
    public $arms;
    /**
     * @param MatchArm[] $arms
     */
    public function __construct(Node\Expr $cond, array $arms = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->arms = $arms;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'arms'];
    }
    public function getType() : string
    {
        return 'Expr_Match';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Expr\\Match_', 'PhpParser\\Node\\Expr\\Match_', \false);

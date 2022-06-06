<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Stmt;

use RevealPrefix20220606\PhpParser\Node;
class Else_ extends Node\Stmt
{
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /**
     * Constructs an else node.
     *
     * @param Node\Stmt[] $stmts      Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $stmts = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->stmts = $stmts;
    }
    public function getSubNodeNames() : array
    {
        return ['stmts'];
    }
    public function getType() : string
    {
        return 'Stmt_Else';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Stmt\\Else_', 'PhpParser\\Node\\Stmt\\Else_', \false);

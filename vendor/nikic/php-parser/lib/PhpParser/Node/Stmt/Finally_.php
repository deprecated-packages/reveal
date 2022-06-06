<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Stmt;

use RevealPrefix20220606\PhpParser\Node;
class Finally_ extends Node\Stmt
{
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /**
     * Constructs a finally node.
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
        return 'Stmt_Finally';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Stmt\\Finally_', 'PhpParser\\Node\\Stmt\\Finally_', \false);

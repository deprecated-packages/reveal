<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node\Stmt;

use RevealPrefix20220606\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames() : array
    {
        return [];
    }
    public function getType() : string
    {
        return 'Stmt_Nop';
    }
}
/** Nop/empty statement (;). */
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\Stmt\\Nop', 'PhpParser\\Node\\Stmt\\Nop', \false);

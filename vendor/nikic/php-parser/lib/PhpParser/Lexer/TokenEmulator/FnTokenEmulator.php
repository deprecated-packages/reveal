<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Lexer\TokenEmulator;

use RevealPrefix20220606\PhpParser\Lexer\Emulative;
final class FnTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return Emulative::PHP_7_4;
    }
    public function getKeywordString() : string
    {
        return 'fn';
    }
    public function getKeywordToken() : int
    {
        return \T_FN;
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Lexer\\TokenEmulator\\FnTokenEmulator', 'PhpParser\\Lexer\\TokenEmulator\\FnTokenEmulator', \false);

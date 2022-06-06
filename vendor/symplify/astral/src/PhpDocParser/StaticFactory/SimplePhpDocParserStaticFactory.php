<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\PhpDocParser\StaticFactory;

use RevealPrefix20220606\PHPStan\PhpDocParser\Lexer\Lexer;
use RevealPrefix20220606\PHPStan\PhpDocParser\Parser\ConstExprParser;
use RevealPrefix20220606\PHPStan\PhpDocParser\Parser\PhpDocParser;
use RevealPrefix20220606\PHPStan\PhpDocParser\Parser\TypeParser;
use RevealPrefix20220606\Symplify\Astral\PhpDocParser\SimplePhpDocParser;
/**
 * @api
 */
final class SimplePhpDocParserStaticFactory
{
    public static function create() : SimplePhpDocParser
    {
        $phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        return new SimplePhpDocParser($phpDocParser, new Lexer());
    }
}

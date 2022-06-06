<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte;

final class LineCommentCorrector
{
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\LineCommentMatcher
     */
    private $lineCommentMatcher;
    public function __construct(LineCommentMatcher $lineCommentMatcher)
    {
        $this->lineCommentMatcher = $lineCommentMatcher;
    }
    /**
     * Move line comments above the line, otherwise php-parser loses them on parsing
     */
    public function correctLineNumberPosition(string $phpContent) : string
    {
        $phpContentLines = \explode(\PHP_EOL, $phpContent);
        $correctedPhpContent = '';
        foreach ($phpContentLines as $phpContentLine) {
            $lineNumber = $this->lineCommentMatcher->matchLine($phpContentLine);
            if ($lineNumber === null) {
                $correctedPhpContent .= $phpContentLine . \PHP_EOL;
                continue;
            }
            $correctedPhpContent .= '/** line in latte file: ' . $lineNumber . ' */ ' . \PHP_EOL;
            $correctedPhpContent .= $phpContentLine;
        }
        return $correctedPhpContent;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\Latte\\LineCommentCorrector', 'Reveal\\LattePHPStanCompiler\\Latte\\LineCommentCorrector', \false);

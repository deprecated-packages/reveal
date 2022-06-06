<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\Tokens;

use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor\LatteLineNumberNodeVisitor;
use RevealPrefix20220606\Symplify\Astral\PhpParser\SmartPhpParser;
final class PhpToLatteLineNumbersResolver
{
    /**
     * @var \Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor\LatteLineNumberNodeVisitor
     */
    private $latteLineNumberNodeVisitor;
    /**
     * @var \Symplify\Astral\PhpParser\SmartPhpParser
     */
    private $smartPhpParser;
    public function __construct(LatteLineNumberNodeVisitor $latteLineNumberNodeVisitor, SmartPhpParser $smartPhpParser)
    {
        $this->latteLineNumberNodeVisitor = $latteLineNumberNodeVisitor;
        $this->smartPhpParser = $smartPhpParser;
    }
    /**
     * Here we have to use file content and parse it again, so we have updated start line positions
     *
     * @return array<int, int>
     */
    public function resolve(string $phpFileContent) : array
    {
        $phpNodes = $this->smartPhpParser->parseString($phpFileContent);
        // nothign to resolve
        if ($phpNodes === []) {
            return [];
        }
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->latteLineNumberNodeVisitor);
        $nodeTraverser->traverse($phpNodes);
        return $this->latteLineNumberNodeVisitor->getPhpLinesToLatteLines();
    }
}

<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\ErrorReporting;

use PhpParser\NodeTraverser;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\PhpToTemplateLinesNodeVisitor;
use RevealPrefix20220705\Symplify\Astral\PhpParser\SmartPhpParser;
final class TemplateLinesMapResolver
{
    /**
     * @var \Symplify\Astral\PhpParser\SmartPhpParser
     */
    private $smartPhpParser;
    public function __construct(SmartPhpParser $smartPhpParser)
    {
        $this->smartPhpParser = $smartPhpParser;
    }
    /**
     * @return array<int, int>
     */
    public function resolve(string $phpContent) : array
    {
        $stmts = $this->smartPhpParser->parseString($phpContent);
        $phpToTemplateLinesNodeVisitor = new PhpToTemplateLinesNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($phpToTemplateLinesNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $phpToTemplateLinesNodeVisitor->getPhpLinesToTemplateLines();
    }
}

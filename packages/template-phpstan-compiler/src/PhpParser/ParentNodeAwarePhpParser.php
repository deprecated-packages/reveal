<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\PhpParser;

use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\NodeVisitor\ParentConnectingVisitor;
use RevealPrefix20220606\Symplify\Astral\PhpParser\SmartPhpParser;
/**
 * @api
 */
final class ParentNodeAwarePhpParser
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
     * @return Stmt[]
     */
    public function parsePhpContent(string $phpContent) : array
    {
        $phpStmts = $this->smartPhpParser->parseString($phpContent);
        if ($phpStmts === []) {
            return [];
        }
        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor(new ParentConnectingVisitor());
        $phpNodeTraverser->traverse($phpStmts);
        return $phpStmts;
    }
}

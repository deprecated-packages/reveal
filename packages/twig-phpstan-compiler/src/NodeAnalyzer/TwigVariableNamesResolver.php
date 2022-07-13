<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\NodeAnalyzer;

use PhpParser\NodeTraverser;
use Reveal\TemplatePHPStanCompiler\Contract\UsedVariableNamesResolverInterface;
use Reveal\TemplatePHPStanCompiler\NodeVisitor\TwigVariableCollectingNodeVisitor;
use Reveal\TemplatePHPStanCompiler\PhpParser\ParentNodeAwarePhpParser;
use Reveal\TwigPHPStanCompiler\TwigToPhpCompiler;
use RevealPrefix20220713\Symplify\Astral\Naming\SimpleNameResolver;
final class TwigVariableNamesResolver implements UsedVariableNamesResolverInterface
{
    /**
     * @var \Reveal\TwigPHPStanCompiler\TwigToPhpCompiler
     */
    private $twigToPhpCompiler;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\PhpParser\ParentNodeAwarePhpParser
     */
    private $parentNodeAwarePhpParser;
    public function __construct(TwigToPhpCompiler $twigToPhpCompiler, SimpleNameResolver $simpleNameResolver, ParentNodeAwarePhpParser $parentNodeAwarePhpParser)
    {
        $this->twigToPhpCompiler = $twigToPhpCompiler;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeAwarePhpParser = $parentNodeAwarePhpParser;
    }
    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath) : array
    {
        $phpFileContentsWithLineMap = $this->twigToPhpCompiler->compileContent($filePath, []);
        $phpFileContents = $phpFileContentsWithLineMap->getPhpFileContents();
        $stmts = $this->parentNodeAwarePhpParser->parsePhpContent($phpFileContents);
        $variableCollectingNodeVisitor = new TwigVariableCollectingNodeVisitor(['context', 'macros', 'this', '_parent', 'loop', 'tmp'], $this->simpleNameResolver);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($variableCollectingNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $variableCollectingNodeVisitor->getUsedVariableNames();
    }
}

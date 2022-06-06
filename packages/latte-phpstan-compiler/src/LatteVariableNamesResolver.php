<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler;

use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeFinder;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\RelatedFileResolver\IncludedSnippetTemplateFileResolver;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\RelatedFileResolver\ParentLayoutTemplateFileResolver;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\Contract\UsedVariableNamesResolverInterface;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeVisitor\TemplateVariableCollectingNodeVisitor;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\PhpParser\ParentNodeAwarePhpParser;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class LatteVariableNamesResolver implements UsedVariableNamesResolverInterface
{
    /**
     * @var \Reveal\TemplatePHPStanCompiler\PhpParser\ParentNodeAwarePhpParser
     */
    private $parentNodeAwarePhpParser;
    /**
     * @var \Reveal\LattePHPStanCompiler\LatteToPhpCompiler
     */
    private $latteToPhpCompiler;
    /**
     * @var \Reveal\LattePHPStanCompiler\RelatedFileResolver\ParentLayoutTemplateFileResolver
     */
    private $parentLayoutTemplateFileResolver;
    /**
     * @var \Reveal\LattePHPStanCompiler\RelatedFileResolver\IncludedSnippetTemplateFileResolver
     */
    private $includedSnippetTemplateFileResolver;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    public function __construct(ParentNodeAwarePhpParser $parentNodeAwarePhpParser, LatteToPhpCompiler $latteToPhpCompiler, ParentLayoutTemplateFileResolver $parentLayoutTemplateFileResolver, IncludedSnippetTemplateFileResolver $includedSnippetTemplateFileResolver, SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->parentNodeAwarePhpParser = $parentNodeAwarePhpParser;
        $this->latteToPhpCompiler = $latteToPhpCompiler;
        $this->parentLayoutTemplateFileResolver = $parentLayoutTemplateFileResolver;
        $this->includedSnippetTemplateFileResolver = $includedSnippetTemplateFileResolver;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
    }
    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath) : array
    {
        $stmts = $this->parseTemplateFileNameToPhpNodes($filePath);
        // resolve parent layout variables
        // 1. current template
        $templateFilePaths = [$filePath];
        // 2. parent layout
        $parentLayoutFileName = $this->parentLayoutTemplateFileResolver->resolve($filePath, $stmts);
        if ($parentLayoutFileName !== null) {
            $templateFilePaths[] = $parentLayoutFileName;
        }
        // 3. included templates
        $includedTemplateFilePaths = $this->includedSnippetTemplateFileResolver->resolve($filePath, $stmts);
        $templateFilePaths = \array_merge($templateFilePaths, $includedTemplateFilePaths);
        $usedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $stmts = $this->parseTemplateFileNameToPhpNodes($templateFilePath);
            $currentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($stmts);
            $usedVariableNames = \array_merge($usedVariableNames, $currentUsedVariableNames);
        }
        return $usedVariableNames;
    }
    /**
     * @param Stmt[] $stmts
     * @return string[]
     */
    private function resolveUsedVariableNamesFromPhpNodes(array $stmts) : array
    {
        $templateVariableCollectingNodeVisitor = new TemplateVariableCollectingNodeVisitor(['this', 'iterations', 'ʟ_l', 'ʟ_v'], ['main'], $this->simpleNameResolver, $this->nodeFinder);
        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor($templateVariableCollectingNodeVisitor);
        $phpNodeTraverser->traverse($stmts);
        return $templateVariableCollectingNodeVisitor->getUsedVariableNames();
    }
    /**
     * @return Stmt[]
     */
    private function parseTemplateFileNameToPhpNodes(string $templateFilePath) : array
    {
        $parentLayoutCompiledPhp = $this->latteToPhpCompiler->compileFilePath($templateFilePath, [], []);
        return $this->parentNodeAwarePhpParser->parsePhpContent($parentLayoutCompiledPhp);
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\LatteVariableNamesResolver', 'Reveal\\LattePHPStanCompiler\\LatteVariableNamesResolver', \false);

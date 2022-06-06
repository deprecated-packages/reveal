<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\RelatedFileResolver;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Reveal\LattePHPStanCompiler\NodeVisitor\TemplateIncludesNameNodeVisitor;
final class IncludedSnippetTemplateFileResolver
{
    /**
     * @var \Reveal\LattePHPStanCompiler\NodeVisitor\TemplateIncludesNameNodeVisitor
     */
    private $templateIncludesNameNodeVisitor;
    public function __construct(TemplateIncludesNameNodeVisitor $templateIncludesNameNodeVisitor)
    {
        $this->templateIncludesNameNodeVisitor = $templateIncludesNameNodeVisitor;
    }
    /**
     * @param Stmt[] $phpNodes
     * @return string[]
     */
    public function resolve(string $templateFilePath, array $phpNodes) : array
    {
        // resolve included templates
        $this->templateIncludesNameNodeVisitor->setTemplateFilePath($templateFilePath);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->templateIncludesNameNodeVisitor);
        $nodeTraverser->traverse($phpNodes);
        return $this->templateIncludesNameNodeVisitor->getIncludedTemplateFilePaths();
    }
}

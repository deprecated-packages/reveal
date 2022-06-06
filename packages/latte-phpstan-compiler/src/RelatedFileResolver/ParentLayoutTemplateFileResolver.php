<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\RelatedFileResolver;

use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\NodeVisitor\ParentLayoutNameNodeVisitor;
final class ParentLayoutTemplateFileResolver
{
    /**
     * @var \Reveal\LattePHPStanCompiler\NodeVisitor\ParentLayoutNameNodeVisitor
     */
    private $parentLayoutNameNodeVisitor;
    public function __construct(ParentLayoutNameNodeVisitor $parentLayoutNameNodeVisitor)
    {
        $this->parentLayoutNameNodeVisitor = $parentLayoutNameNodeVisitor;
    }
    /**
     * @param Stmt[] $phpNodes
     */
    public function resolve(string $templateFilePath, array $phpNodes) : ?string
    {
        $phpNodeTraverser = new NodeTraverser();
        $this->parentLayoutNameNodeVisitor->setTemplateFilePath($templateFilePath);
        $phpNodeTraverser->addVisitor($this->parentLayoutNameNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);
        return $this->parentLayoutNameNodeVisitor->getParentLayoutFileName();
    }
}

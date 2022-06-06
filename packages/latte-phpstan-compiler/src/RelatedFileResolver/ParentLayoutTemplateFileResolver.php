<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\RelatedFileResolver;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Reveal\LattePHPStanCompiler\NodeVisitor\ParentLayoutNameNodeVisitor;
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

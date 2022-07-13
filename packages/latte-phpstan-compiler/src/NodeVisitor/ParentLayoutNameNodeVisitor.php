<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use RevealPrefix20220713\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220713\Symplify\Astral\NodeValue\NodeValueResolver;
use RevealPrefix20220713\Symplify\SmartFileSystem\SmartFileSystem;
final class ParentLayoutNameNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $templateFilePath = '';
    /**
     * @var string|null
     */
    private $parentLayoutFileName = null;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symplify\Astral\NodeValue\NodeValueResolver
     */
    private $nodeValueResolver;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SmartFileSystem $smartFileSystem, NodeValueResolver $nodeValueResolver, SimpleNameResolver $simpleNameResolver)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->nodeValueResolver = $nodeValueResolver;
        $this->simpleNameResolver = $simpleNameResolver;
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        // reset to avoid template file in next analysed file
        $this->parentLayoutFileName = null;
        return $nodes;
    }
    public function setTemplateFilePath(string $templateFilePath) : void
    {
        $this->templateFilePath = $templateFilePath;
    }
    /**
     * @return null|int
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Assign) {
            return null;
        }
        $parentLayoutTemplate = $this->matchParentLayoutName($node);
        if ($parentLayoutTemplate === null) {
            return null;
        }
        // find and analyse?
        $currentFileRealPath = \realpath($this->templateFilePath);
        $layoutTemplateFilePath = \dirname($currentFileRealPath) . '/' . $parentLayoutTemplate;
        if (!$this->smartFileSystem->exists($layoutTemplateFilePath)) {
            return null;
        }
        $this->parentLayoutFileName = $layoutTemplateFilePath;
        return NodeTraverser::STOP_TRAVERSAL;
    }
    public function getParentLayoutFileName() : ?string
    {
        return $this->parentLayoutFileName;
    }
    /**
     * @return string|null
     */
    private function matchParentLayoutName(Assign $assign)
    {
        if (!$assign->var instanceof PropertyFetch) {
            return null;
        }
        $propertyFetch = $assign->var;
        if (!$this->simpleNameResolver->isName($propertyFetch->var, 'this')) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($propertyFetch->name, 'parentName')) {
            return null;
        }
        return $this->nodeValueResolver->resolve($assign->expr, $this->templateFilePath);
    }
}

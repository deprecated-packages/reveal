<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\SmartFileSystem\SmartFileSystem;
final class TemplateIncludesNameNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $includedTemplateFilePaths = [];
    /**
     * @var string
     */
    private $templateFilePath = '';
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
    public function setTemplateFilePath(string $templateFilePath) : void
    {
        $this->templateFilePath = $templateFilePath;
    }
    /**
     * @return null|int
     */
    public function enterNode(Node $node)
    {
        // match $this->createTemplate('anything.latte')
        if (!$node instanceof MethodCall) {
            return null;
        }
        $includedTemplateName = $this->matchIncludedTemplateName($node);
        if ($includedTemplateName === null) {
            return null;
        }
        // find and analyse?
        $currentFileRealPath = \realpath($this->templateFilePath);
        $includedTemplateFilePath = \dirname($currentFileRealPath) . '/' . $includedTemplateName;
        if (!$this->smartFileSystem->exists($includedTemplateFilePath)) {
            return null;
        }
        $this->includedTemplateFilePaths[] = $includedTemplateFilePath;
        return null;
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        // reset to avoid keeping old variables for new template
        $this->includedTemplateFilePaths = [];
        return $nodes;
    }
    /**
     * @return string[]
     */
    public function getIncludedTemplateFilePaths() : array
    {
        return $this->includedTemplateFilePaths;
    }
    /**
     * @return string|null
     */
    private function matchIncludedTemplateName(MethodCall $methodCall)
    {
        if (!$this->simpleNameResolver->isName($methodCall->var, 'this')) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($methodCall->name, 'createTemplate')) {
            return null;
        }
        $argOrVariadicPlaceholder = $methodCall->args[0];
        if (!$argOrVariadicPlaceholder instanceof Arg) {
            return null;
        }
        $firstArgValue = $argOrVariadicPlaceholder->value;
        return $this->nodeValueResolver->resolve($firstArgValue, $this->templateFilePath);
    }
}

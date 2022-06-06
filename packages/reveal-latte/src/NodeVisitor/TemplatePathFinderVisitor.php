<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use RevealPrefix20220606\Symplify\Astral\NodeValue\NodeValueResolver;
final class TemplatePathFinderVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $templatePaths = [];
    /**
     * @var \PHPStan\Analyser\Scope
     */
    private $scope;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer
     */
    private $netteTypeAnalyzer;
    /**
     * @var \Symplify\Astral\NodeValue\NodeValueResolver
     */
    private $nodeValueResolver;
    public function __construct(Scope $scope, SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $netteTypeAnalyzer, NodeValueResolver $nodeValueResolver)
    {
        $this->scope = $scope;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->netteTypeAnalyzer = $netteTypeAnalyzer;
        $this->nodeValueResolver = $nodeValueResolver;
    }
    /**
     * @return null|\PhpParser\Node
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof MethodCall) {
            return null;
        }
        $methodName = $this->simpleNameResolver->getName($node->name);
        if (!\in_array($methodName, ['setFile', 'render', 'renderToString'], \true)) {
            return null;
        }
        if (!$this->netteTypeAnalyzer->isTemplateType($node->var, $this->scope)) {
            return null;
        }
        $pathArg = $node->getArgs()[0] ?? null;
        if (!$pathArg instanceof Arg) {
            return null;
        }
        $path = $this->nodeValueResolver->resolveWithScope($pathArg->value, $this->scope);
        if (\is_string($path)) {
            $this->templatePaths[] = $path;
        }
        return null;
    }
    /**
     * call after traversing
     *
     * @return string[]
     */
    public function getTemplatePaths() : array
    {
        return $this->templatePaths;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\RevealLatte\\NodeVisitor\\TemplatePathFinderVisitor', 'Reveal\\RevealLatte\\NodeVisitor\\TemplatePathFinderVisitor', \false);

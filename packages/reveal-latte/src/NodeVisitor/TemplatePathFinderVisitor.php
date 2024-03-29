<?php

declare (strict_types=1);
namespace Reveal\RevealLatte\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use Symplify\Astral\NodeValue\NodeValueResolver;
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
        $path = $this->nodeValueResolver->resolve($pathArg->value, $this->scope->getFile());
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

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
final class RenderParametersVisitor extends NodeVisitorAbstract
{
    /**
     * @var ArrayItem[]
     */
    private $parameters = [];
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
    public function __construct(Scope $scope, SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $netteTypeAnalyzer)
    {
        $this->scope = $scope;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->netteTypeAnalyzer = $netteTypeAnalyzer;
    }
    public function enterNode(Node $node)
    {
        if (!$node instanceof MethodCall) {
            return null;
        }
        $methodName = $this->simpleNameResolver->getName($node->name);
        if (!\in_array($methodName, ['render', 'renderToString'], \true)) {
            return null;
        }
        if (!$this->netteTypeAnalyzer->isTemplateType($node->var, $this->scope)) {
            return null;
        }
        $renderParameters = $node->args[1] ?? null;
        if (!$renderParameters instanceof Arg) {
            return null;
        }
        $parameters = $renderParameters->value;
        if (!$parameters instanceof Array_) {
            return null;
        }
        $this->parameters = \array_filter($parameters->items);
        return null;
    }
    /**
     * call after traversing
     *
     * @return ArrayItem[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
}

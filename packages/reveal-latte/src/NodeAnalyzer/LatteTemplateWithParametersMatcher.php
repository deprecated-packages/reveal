<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Stmt\Class_;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor\AssignedParametersVisitor;
use RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor\RenderParametersVisitor;
use RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor\TemplatePathFinderVisitor;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
use RevealPrefix20220606\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use RevealPrefix20220606\Symplify\Astral\NodeValue\NodeValueResolver;
final class LatteTemplateWithParametersMatcher
{
    /**
     * @var \Symplify\Astral\NodeFinder\SimpleNodeFinder
     */
    private $simpleNodeFinder;
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
    public function __construct(SimpleNodeFinder $simpleNodeFinder, SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $netteTypeAnalyzer, NodeValueResolver $nodeValueResolver)
    {
        $this->simpleNodeFinder = $simpleNodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->netteTypeAnalyzer = $netteTypeAnalyzer;
        $this->nodeValueResolver = $nodeValueResolver;
    }
    /**
     * @return RenderTemplateWithParameters[]
     */
    public function match(MethodCall $methodCall, Scope $scope) : array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (!$class instanceof Class_) {
            return [];
        }
        $templates = $this->findTemplates($class, $scope);
        if ($templates === []) {
            return [];
        }
        $parameters = $this->findParameters($class, $scope);
        $result = [];
        foreach ($templates as $template) {
            $result[] = new RenderTemplateWithParameters($template, new Array_($parameters));
        }
        return $result;
    }
    /**
     * @return ArrayItem[]
     */
    public function findParameters(Node $node, Scope $scope) : array
    {
        $nodes = [$node];
        $nodeTraverser = new NodeTraverser();
        $assignedParametersVisitor = new AssignedParametersVisitor($scope, $this->simpleNameResolver, $this->netteTypeAnalyzer);
        $renderParametersVisitor = new RenderParametersVisitor($scope, $this->simpleNameResolver, $this->netteTypeAnalyzer);
        $nodeTraverser->addVisitor($assignedParametersVisitor);
        $nodeTraverser->addVisitor($renderParametersVisitor);
        $nodeTraverser->traverse($nodes);
        return \array_merge($assignedParametersVisitor->getParameters(), $renderParametersVisitor->getParameters());
    }
    /**
     * @return string[]
     */
    private function findTemplates(Class_ $class, Scope $scope) : array
    {
        $nodes = [$class];
        $nodeTraverser = new NodeTraverser();
        $templatePathFinderVisitor = new TemplatePathFinderVisitor($scope, $this->simpleNameResolver, $this->netteTypeAnalyzer, $this->nodeValueResolver);
        $nodeTraverser->addVisitor($templatePathFinderVisitor);
        $nodeTraverser->traverse($nodes);
        return $templatePathFinderVisitor->getTemplatePaths();
    }
}

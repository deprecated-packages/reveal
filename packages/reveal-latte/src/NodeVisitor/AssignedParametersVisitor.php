<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PhpParser\Node\Expr\Assign;
use RevealPrefix20220606\PhpParser\Node\Expr\PropertyFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Scalar\String_;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
final class AssignedParametersVisitor extends NodeVisitorAbstract
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
    /**
     * @return null|\PhpParser\Node
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Assign) {
            return null;
        }
        if ($node->var instanceof Variable) {
            $var = $node->var;
            $nameNode = $node->var->name;
        } elseif ($node->var instanceof PropertyFetch) {
            $var = $node->var->var;
            $nameNode = $node->var->name;
        } else {
            return null;
        }
        if (!$this->netteTypeAnalyzer->isTemplateType($var, $this->scope)) {
            return null;
        }
        $name = $this->simpleNameResolver->getName($nameNode);
        if (!$name) {
            return null;
        }
        $this->parameters[] = new ArrayItem($node->expr, new String_($name));
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

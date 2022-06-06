<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\Normalization;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Name;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use Reveal\TwigPHPStanCompiler\Contract\NodeVisitor\NormalizingNodeVisitorInterface;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
final class LoadTemplateNormalizeNodeVisitor extends NodeVisitorAbstract implements NormalizingNodeVisitorInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function enterNode(Node $node)
    {
        if (!$node instanceof MethodCall) {
            return null;
        }
        if (!$this->simpleNameResolver->isName($node->name, 'display')) {
            return null;
        }
        if (!$node->var instanceof MethodCall) {
            return null;
        }
        $loadTemplateMethodCall = $node->var;
        $templateExpr = $loadTemplateMethodCall->getArgs()[0]->value;
        // complete parameters
        $args = [new Arg($templateExpr)];
        $firstArgValue = $node->getArgs()[0]->value;
        // func call merge @todo
        if ($firstArgValue instanceof FuncCall && $this->simpleNameResolver->isName($firstArgValue, 'twig_array_merge')) {
            $parameterArray = $firstArgValue->getArgs()[1]->value;
            $args[] = new Arg($parameterArray);
        }
        return new FuncCall(new Name('load_template'), $args);
    }
}

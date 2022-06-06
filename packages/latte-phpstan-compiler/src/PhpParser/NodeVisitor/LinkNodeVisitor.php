<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PhpParser\Node\Expr\ArrayItem;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\PropertyFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\StaticCall;
use RevealPrefix20220606\PhpParser\Node\Stmt\Echo_;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LinkProcessorInterface;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Exception\LattePHPStanCompilerException;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\LinkProcessor\LinkProcessorFactory;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeValue\NodeValueResolver;
final class LinkNodeVisitor extends NodeVisitorAbstract implements LatteToPhpCompilerNodeVisitorInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\NodeValue\NodeValueResolver
     */
    private $nodeValueResolver;
    /**
     * @var \Reveal\LattePHPStanCompiler\LinkProcessor\LinkProcessorFactory
     */
    private $linkProcessorFactory;
    public function __construct(SimpleNameResolver $simpleNameResolver, NodeValueResolver $nodeValueResolver, LinkProcessorFactory $linkProcessorFactory)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeValueResolver = $nodeValueResolver;
        $this->linkProcessorFactory = $linkProcessorFactory;
    }
    /**
     * @return Node[]|null
     */
    public function leaveNode(Node $node) : ?array
    {
        if (!$node instanceof Echo_) {
            return null;
        }
        $staticCall = $node->exprs[0] ?? null;
        if (!$staticCall instanceof StaticCall) {
            return null;
        }
        if (\count($staticCall->getArgs()) !== 1) {
            return null;
        }
        $arg = $staticCall->getArgs()[0];
        $methodCall = $arg->value;
        if (!$methodCall instanceof MethodCall) {
            return null;
        }
        if (!$this->isMethodCallUiLink($methodCall)) {
            return null;
        }
        return $this->prepareNodes($methodCall, $node->getAttributes());
    }
    /**
     * @param array<string, mixed> $attributes
     * @return Node[]|null
     */
    private function prepareNodes(MethodCall $methodCall, array $attributes) : ?array
    {
        $linkArgs = $methodCall->getArgs();
        $target = $linkArgs[0]->value;
        $targetName = $this->nodeValueResolver->resolve($target, '');
        if (!\is_string($targetName)) {
            throw new LattePHPStanCompilerException();
        }
        $targetName = \ltrim($targetName, '/');
        $linkProcessor = $this->linkProcessorFactory->create($targetName);
        if (!$linkProcessor instanceof LinkProcessorInterface) {
            return null;
        }
        $targetParams = isset($linkArgs[1]) ? $linkArgs[1]->value : null;
        $linkParams = $targetParams instanceof Array_ ? $this->createLinkParams($targetParams) : [];
        $expressions = $linkProcessor->createLinkExpressions($targetName, $linkParams, $attributes);
        if ($expressions === []) {
            return null;
        }
        return $expressions;
    }
    private function isMethodCallUiLink(MethodCall $methodCall) : bool
    {
        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodName !== 'link') {
            return \false;
        }
        $propertyFetch = $methodCall->var;
        if (!$propertyFetch instanceof PropertyFetch) {
            return \false;
        }
        $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);
        return \in_array($propertyFetchName, ['uiControl', 'uiPresenter'], \true);
    }
    /**
     * @return Arg[]
     */
    private function createLinkParams(Array_ $array) : array
    {
        $linkParams = [];
        foreach ($array->items as $arrayItem) {
            if (!$arrayItem instanceof ArrayItem) {
                continue;
            }
            $linkParams[] = new Arg($arrayItem);
        }
        return $linkParams;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\LinkNodeVisitor', 'Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\LinkNodeVisitor', \false);

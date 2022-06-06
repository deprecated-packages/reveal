<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\PropertyFetch;
use RevealPrefix20220606\PhpParser\Node\Expr\StaticCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Identifier;
use RevealPrefix20220606\PhpParser\Node\Name\FullyQualified;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\Filters\FilterMatcher;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\DynamicCallReference;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\StaticCallReference;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * Make \Latte\Runtime\Defaults::getFilters() explicit, from: $this->filters->{magic}(...)
 *
 * to: \Latte\Runtime\Filters::date(...)
 */
final class MagicFilterToExplicitCallNodeVisitor extends NodeVisitorAbstract implements LatteToPhpCompilerNodeVisitorInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\Filters\FilterMatcher
     */
    private $filterMatcher;
    public function __construct(SimpleNameResolver $simpleNameResolver, FilterMatcher $filterMatcher)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->filterMatcher = $filterMatcher;
    }
    /**
     * Looking for: "$this->filters->{magic}"
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof FuncCall) {
            return null;
        }
        if (!$node->name instanceof Expr) {
            return null;
        }
        $dynamicName = $node->name;
        if (!$dynamicName instanceof PropertyFetch) {
            return null;
        }
        if (!$this->isPropertyFetchNames($dynamicName->var, 'this', 'filters')) {
            return null;
        }
        $filterName = $this->simpleNameResolver->getName($dynamicName->name);
        if ($filterName === null) {
            return null;
        }
        $callReference = $this->filterMatcher->match($filterName);
        $args = $node->args;
        // Add FilterInfo for special filters
        if (\in_array($filterName, ['striphtml', 'striptags', 'strip', 'indent', 'repeat', 'replace', 'trim'], \true)) {
            $args = \array_merge([new Arg(new Variable('ʟ_fi'))], $args);
        }
        if ($callReference instanceof StaticCallReference) {
            return new StaticCall(new FullyQualified($callReference->getClass()), new Identifier($callReference->getMethod()), $args);
        }
        if ($callReference instanceof DynamicCallReference) {
            $className = $callReference->getClass();
            $variableName = Strings::firstLower(Strings::replace($className, '#\\\\#', '')) . 'Filter';
            return new MethodCall(new Variable($variableName), new Identifier($callReference->getMethod()), $args);
        }
        if ($callReference instanceof FunctionCallReference) {
            return new FuncCall(new FullyQualified($callReference->getFunction()), $args);
        }
        return null;
    }
    private function isPropertyFetchNames(Expr $expr, string $variableName, string $propertyName) : bool
    {
        if (!$expr instanceof PropertyFetch) {
            return \false;
        }
        if (!$this->simpleNameResolver->isName($expr->var, $variableName)) {
            return \false;
        }
        return $this->simpleNameResolver->isName($expr->name, $propertyName);
    }
}

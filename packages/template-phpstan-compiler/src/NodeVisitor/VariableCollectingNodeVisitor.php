<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\Assign;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\Node\Stmt\Foreach_;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\ValueObject\AttributeKey;
/**
 * @api
 */
final class VariableCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $userVariableNames = [];
    /**
     * @var string[]
     */
    private $justCreatedVariableNames = [];
    /**
     * @var array<string>
     */
    private $defaultVariableNames;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @param array<string> $defaultVariableNames
     */
    public function __construct(array $defaultVariableNames, SimpleNameResolver $simpleNameResolver)
    {
        $this->defaultVariableNames = $defaultVariableNames;
        $this->simpleNameResolver = $simpleNameResolver;
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->reset();
        return $nodes;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Variable) {
            return null;
        }
        $variableName = $this->simpleNameResolver->getName($node);
        if ($variableName === null) {
            return null;
        }
        if ($this->isJustCreatedVariable($node)) {
            $this->justCreatedVariableNames[] = $variableName;
            return null;
        }
        $this->userVariableNames[] = $variableName;
        return null;
    }
    /**
     * @return string[]
     */
    public function getUsedVariableNames() : array
    {
        $removedVariableNames = \array_merge($this->defaultVariableNames, $this->justCreatedVariableNames);
        $usedVariableNames = \array_diff($this->userVariableNames, $removedVariableNames);
        return \array_unique($usedVariableNames);
    }
    private function reset() : void
    {
        // reset to avoid used variable name in next analysed file
        $this->userVariableNames = [];
        $this->justCreatedVariableNames = [];
    }
    private function isJustCreatedVariable(Variable $variable) : bool
    {
        $parent = $variable->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Assign && $parent->var === $variable) {
            return \true;
        }
        if (!$parent instanceof Foreach_) {
            return \false;
        }
        return $parent->valueVar === $variable;
    }
}

<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\TypeWithClassName;
use Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\Astral\Naming\SimpleNameResolver;
/**
 * Make $_tmp = $this->global->uiControl->getComponent("someName");
 *
 * to: /** @var SomeTypeControl $someNameControl $someNameControl = ...
 */
final class ControlRenderToExplicitCallNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private const TMP_NAME = '_tmp';
    /**
     * @var string|null
     */
    private $currentComponentName = null;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var ComponentNameAndType[]
     */
    private $componentNamesAndTypes;
    /**
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $componentNamesAndTypes)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->componentNamesAndTypes = $componentNamesAndTypes;
    }
    /**
     * @param Node\Stmt[] $nodes
     * @return Node\Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->currentComponentName = null;
        return $nodes;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Expression && $node->expr instanceof Assign) {
            return $this->processAssign($node->expr, $node);
        }
        if ($node instanceof Variable) {
            return $this->processVariable($node);
        }
        return null;
    }
    private function resolveAssignedComponentName(Expr $expr) : ?string
    {
        if (!$expr instanceof MethodCall) {
            return null;
        }
        $methodCall = $expr;
        if (!$this->simpleNameResolver->isName($methodCall->name, 'getComponent')) {
            return null;
        }
        $firstArg = $methodCall->getArgs()[0];
        // try to get component name
        if (!$firstArg->value instanceof String_) {
            return null;
        }
        return $firstArg->value->value;
    }
    /**
     * Looking for assign: $tmp_ = $this->global->uiControl->getComponent("someName");
     * @return \PhpParser\Node\Stmt\Expression|null
     */
    private function processAssign(Assign $assign, Expression $expression)
    {
        // look for $tmp_
        if (!$this->simpleNameResolver->isName($assign->var, self::TMP_NAME)) {
            return null;
        }
        $componentName = $this->resolveAssignedComponentName($assign->expr);
        if ($componentName === null) {
            return null;
        }
        $this->currentComponentName = $componentName . 'Control';
        // 1. rename assigned control
        $assign->var = new Variable($this->currentComponentName);
        // 2. add @var type
        foreach ($this->componentNamesAndTypes as $componentNameAndType) {
            if ($componentNameAndType->getName() !== $componentName) {
                continue;
            }
            $componentType = $componentNameAndType->getReturnType();
            if (!$componentType instanceof TypeWithClassName) {
                continue;
            }
            $resolvedComponentName = $componentType->getClassName();
            $varDocBlockText = \sprintf('/** @var \\%s $%s */', $resolvedComponentName, $this->currentComponentName);
            $this->appendDocCommentToNode($expression, $varDocBlockText);
        }
        return $expression;
    }
    private function appendDocCommentToNode(Expression $expression, string $varDocBlockText) : void
    {
        $newDocText = $varDocBlockText;
        $originalDoc = $expression->getDocComment();
        if ($originalDoc instanceof Doc) {
            $newDocText .= \PHP_EOL . $originalDoc->getText();
        }
        $expression->setDocComment(new Doc($newDocText));
    }
    private function processVariable(Variable $variable) : ?Variable
    {
        if (!$this->simpleNameResolver->isName($variable, self::TMP_NAME)) {
            return null;
        }
        if ($this->currentComponentName === null) {
            return null;
        }
        return new Variable($this->currentComponentName);
    }
}

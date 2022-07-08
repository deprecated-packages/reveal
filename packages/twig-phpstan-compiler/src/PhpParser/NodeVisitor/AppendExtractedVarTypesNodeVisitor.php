<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220708\Symplify\Astral\Naming\SimpleNameResolver;
final class AppendExtractedVarTypesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory
     */
    private $varDocNodeFactory;
    /**
     * @var VariableAndType[]
     */
    private $variablesAndTypes;
    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, VarDocNodeFactory $varDocNodeFactory, array $variablesAndTypes)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->varDocNodeFactory = $varDocNodeFactory;
        $this->variablesAndTypes = $variablesAndTypes;
    }
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        // look for "doDisplay()"
        if (!$node instanceof ClassMethod) {
            return null;
        }
        if (!$this->simpleNameResolver->isNames($node, ['doDisplay', 'block_*'])) {
            return null;
        }
        $docNodes = $this->varDocNodeFactory->createDocNodes($this->variablesAndTypes);
        // needed to ping phpstan about possible invisbile variables
        $extractFuncCall = new FuncCall(new Name('extract'));
        $extractFuncCall->args[] = new Arg(new Variable('context'));
        $funcCallExpression = new Expression($extractFuncCall);
        $node->stmts = \array_merge([$funcCallExpression], $docNodes, (array) $node->stmts);
        return $node;
    }
}

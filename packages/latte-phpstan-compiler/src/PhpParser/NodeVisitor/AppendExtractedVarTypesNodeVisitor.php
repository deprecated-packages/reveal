<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
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
        if (!$node instanceof ClassMethod) {
            return null;
        }
        // nothing to wrap
        if ($node->stmts === null) {
            return null;
        }
        foreach ($node->stmts as $key => $classMethodStmt) {
            if (!$classMethodStmt instanceof Expression) {
                continue;
            }
            $extractMethodCall = $classMethodStmt->expr;
            if (!$extractMethodCall instanceof FuncCall) {
                continue;
            }
            if (!$this->simpleNameResolver->isName($extractMethodCall, 'extract')) {
                continue;
            }
            $docNodes = $this->varDocNodeFactory->createDocNodes($this->variablesAndTypes);
            // must be AFTER extract(), otherwise the variable does not exists
            \array_splice($node->stmts, $key + 1, 0, $docNodes);
            return $node;
        }
        return null;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\AppendExtractedVarTypesNodeVisitor', 'Reveal\\LattePHPStanCompiler\\PhpParser\\NodeVisitor\\AppendExtractedVarTypesNodeVisitor', \false);

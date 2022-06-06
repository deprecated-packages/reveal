<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Stmt\ClassMethod;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
final class RemoveUselessClassMethodsNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const METHOD_NAMES_TO_REMOVE = ['getTemplateName', 'isTraitable', 'getDebugInfo', 'getSourceContext'];
    /**
     * @return null|int
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }
        $classMethodName = $node->name->toString();
        if (!\in_array($classMethodName, self::METHOD_NAMES_TO_REMOVE, \true)) {
            return null;
        }
        return NodeTraverser::REMOVE_NODE;
    }
}

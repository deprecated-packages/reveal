<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\Assign;
use RevealPrefix20220606\PhpParser\Node\Expr\BinaryOp\Concat;
use RevealPrefix20220606\PhpParser\Node\Expr\FuncCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Ternary;
use RevealPrefix20220606\PhpParser\Node\Name\FullyQualified;
use RevealPrefix20220606\PhpParser\Node\Scalar\String_;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
/**
 * from: <code> echo ($ʟ_tmp = \array_filter(['class1', $var ? 'class2' : \null])) ? ' class="' .
 * \Latte\Runtime\Filters::escapeHtmlAttr(\implode(" ", \array_unique($ʟ_tmp))) . '"' : ""; </code>
 *
 * to: <code> echo ' class="' . \implode(" ", ['class1', $var ? 'class2' : \null]) . '"'; </code>
 */
final class NClassNodeVisitor extends NodeVisitorAbstract implements LatteToPhpCompilerNodeVisitorInterface
{
    /**
     * @return \PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Ternary) {
            return null;
        }
        // looking for `class="' . \Latte\Runtime\Filters::escapeHtmlAttr()`
        if (!$node->if instanceof Concat) {
            return null;
        }
        if (!$node->if->left instanceof Concat) {
            return null;
        }
        if (!$node->if->left->left instanceof String_) {
            return null;
        }
        $left = $node->if->left->left;
        if ($left->value !== ' class="') {
            return null;
        }
        if (!$node->cond instanceof Assign) {
            return null;
        }
        if (!$node->cond->expr instanceof FuncCall) {
            return null;
        }
        /** @var FuncCall $funcCall */
        $funcCall = $node->cond->expr;
        if (!isset($funcCall->args[0])) {
            return null;
        }
        $implodeSeparatorString = new String_(' ');
        $args = [new Arg($implodeSeparatorString), $funcCall->args[0]];
        $implode = new FuncCall(new FullyQualified('implode'), $args);
        return new Concat(new Concat($left, $implode), $node->if->right);
    }
}

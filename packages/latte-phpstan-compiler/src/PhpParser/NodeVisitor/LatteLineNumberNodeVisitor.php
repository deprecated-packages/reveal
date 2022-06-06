<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\PhpParser\Comment\Doc;
use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use Reveal\LattePHPStanCompiler\Latte\LineCommentMatcher;
final class LatteLineNumberNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<int, int>
     */
    private $phpLinesToLatteLines = [];
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\LineCommentMatcher
     */
    private $lineCommentMatcher;
    public function __construct(LineCommentMatcher $lineCommentMatcher)
    {
        $this->lineCommentMatcher = $lineCommentMatcher;
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        // reset to avoid leak to another class
        $this->phpLinesToLatteLines = [];
        return $nodes;
    }
    public function enterNode(Node $node)
    {
        $docComment = $node->getDocComment();
        if (!$docComment instanceof Doc) {
            return null;
        }
        $docCommentText = $docComment->getText();
        $latteLine = $this->lineCommentMatcher->matchLine($docCommentText);
        if ($latteLine === null) {
            return null;
        }
        $this->phpLinesToLatteLines[$node->getStartLine()] = $latteLine;
        return null;
    }
    /**
     * @return array<int, int>
     */
    public function getPhpLinesToLatteLines() : array
    {
        return $this->phpLinesToLatteLines;
    }
}

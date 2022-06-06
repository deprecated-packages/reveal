<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
final class PhpToTemplateLinesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     * @see https://regex101.com/r/eQiVfK/1
     */
    private const TWIG_LINE_REGEX = '#\\/\\/ line (?<line_number>\\d+)#';
    /**
     * @var array<int, int>
     */
    private $phpLinesToTemplateLines = [];
    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->phpLinesToTemplateLines = [];
        return $nodes;
    }
    public function enterNode(Node $node)
    {
        if (!$node instanceof Stmt) {
            return null;
        }
        if ($node->getComments() === []) {
            return null;
        }
        foreach ($node->getComments() as $comment) {
            $match = Strings::match($comment->getText(), self::TWIG_LINE_REGEX);
            if ($match === null) {
                continue;
            }
            $templateLineNumber = (int) $match['line_number'];
            $phpLineNumber = $node->getLine();
            $this->phpLinesToTemplateLines[$phpLineNumber] = $templateLineNumber;
        }
        return null;
    }
    /**
     * @return array<int, int>
     */
    public function getPhpLinesToTemplateLines() : array
    {
        return $this->phpLinesToTemplateLines;
    }
}

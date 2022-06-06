<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Scalar\String_;
use RevealPrefix20220606\PhpParser\Node\Stmt\Echo_;
use RevealPrefix20220606\PhpParser\Node\Stmt\Nop;
use RevealPrefix20220606\PhpParser\NodeVisitorAbstract;
use RevealPrefix20220606\PHPStan\Type\ObjectType;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\DocBlock\NonVarTypeDocBlockCleaner;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\ValueObject\VarTypeDoc;
final class ReplaceEchoWithVarDocTypeNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var VariableAndType[]
     */
    private $collectedVariablesAndTypes = [];
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->collectedVariablesAndTypes = [];
        return parent::beforeTraverse($nodes);
    }
    public function enterNode(Node $node)
    {
        if (!$node instanceof Echo_) {
            return null;
        }
        if (\count($node->exprs) !== 1) {
            return null;
        }
        if (!$node->exprs[0] instanceof String_) {
            return null;
        }
        $string = $node->exprs[0];
        $match = Strings::match($string->value, NonVarTypeDocBlockCleaner::TWIG_VAR_TYPE_DOCBLOCK_REGEX);
        if ($match === null) {
            return null;
        }
        $varTypeDoc = new VarTypeDoc($match['name'], $match['type']);
        // @todo assumption that type is an object - resolve in some strict/doc parser clean way
        $this->collectedVariablesAndTypes[] = new VariableAndType($varTypeDoc->getVariableName(), new ObjectType($varTypeDoc->getType()));
        // basically remove node
        return new Nop();
    }
    /**
     * @return VariableAndType[]
     */
    public function getCollectedVariablesAndTypes() : array
    {
        return $this->collectedVariablesAndTypes;
    }
}

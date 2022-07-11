<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;
use Reveal\LattePHPStanCompiler\Exception\LattePHPStanCompilerException;
use Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220711\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220711\Symplify\Astral\PhpParser\SmartPhpParser;
final class LatteVarTypeDocBlockDecorator
{
    /**
     * @var \Symplify\Astral\PhpParser\SmartPhpParser
     */
    private $smartPhpParser;
    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory
     */
    private $varDocNodeFactory;
    public function __construct(SmartPhpParser $smartPhpParser, Standard $printerStandard, SimpleNameResolver $simpleNameResolver, VarDocNodeFactory $varDocNodeFactory)
    {
        $this->smartPhpParser = $smartPhpParser;
        $this->printerStandard = $printerStandard;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->varDocNodeFactory = $varDocNodeFactory;
    }
    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function decorateLatteContentWithTypes(string $phpContent, array $variablesAndTypes) : string
    {
        // convert to "@var types $variable"
        $phpStmts = $this->smartPhpParser->parseString($phpContent);
        if ($phpStmts === []) {
            throw new LattePHPStanCompilerException();
        }
        $nodeTraverser = new NodeTraverser();
        $appendExtractedVarTypesNodeVisitor = new AppendExtractedVarTypesNodeVisitor($this->simpleNameResolver, $this->varDocNodeFactory, $variablesAndTypes);
        $nodeTraverser->addVisitor($appendExtractedVarTypesNodeVisitor);
        $nodeTraverser->traverse($phpStmts);
        $printedPhpContent = $this->printerStandard->prettyPrintFile($phpStmts);
        return \rtrim($printedPhpContent) . \PHP_EOL;
    }
}

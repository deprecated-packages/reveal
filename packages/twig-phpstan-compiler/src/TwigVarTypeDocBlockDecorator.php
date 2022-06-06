<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\TwigPHPStanCompiler;

use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\PrettyPrinter\Standard;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\PhpParser\SmartPhpParser;
final class TwigVarTypeDocBlockDecorator
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
    public function decorateTwigContentWithTypes(string $phpContent, array $variablesAndTypes) : string
    {
        // convert to "@var types $variable"
        $phpNodes = $this->smartPhpParser->parseString($phpContent);
        $nodeTraverser = new NodeTraverser();
        $appendExtractedVarTypesNodeVisitor = new AppendExtractedVarTypesNodeVisitor($this->simpleNameResolver, $this->varDocNodeFactory, $variablesAndTypes);
        $nodeTraverser->addVisitor($appendExtractedVarTypesNodeVisitor);
        $nodeTraverser->traverse($phpNodes);
        $printedPhpContent = $this->printerStandard->prettyPrintFile($phpNodes);
        return \rtrim($printedPhpContent) . \PHP_EOL;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\TwigPHPStanCompiler\\TwigVarTypeDocBlockDecorator', 'Reveal\\TwigPHPStanCompiler\\TwigVarTypeDocBlockDecorator', \false);

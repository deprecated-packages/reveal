<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler;

use RevealPrefix20220606\Latte\Parser;
use RevealPrefix20220606\PhpParser\Node\Stmt;
use RevealPrefix20220606\PhpParser\NodeTraverser;
use RevealPrefix20220606\PhpParser\ParserFactory;
use RevealPrefix20220606\PhpParser\PrettyPrinter\Standard;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteToPhpCompilerNodeVisitorInterface;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\LineCommentCorrector;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\UnknownMacroAwareLatteCompiler;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\PhpParser\NodeVisitor\ControlRenderToExplicitCallNodeVisitor;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use RevealPrefix20220606\Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @see \Reveal\LattePHPStanCompiler\Tests\LatteToPhpCompiler\LatteToPhpCompilerTest
 */
final class LatteToPhpCompiler
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Latte\Parser
     */
    private $latteParser;
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\UnknownMacroAwareLatteCompiler
     */
    private $unknownMacroAwareLatteCompiler;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\LineCommentCorrector
     */
    private $lineCommentCorrector;
    /**
     * @var \Reveal\LattePHPStanCompiler\LatteVarTypeDocBlockDecorator
     */
    private $latteVarTypeDocBlockDecorator;
    /**
     * @var LatteToPhpCompilerNodeVisitorInterface[]
     */
    private $nodeVisitors;
    /**
     * @param LatteToPhpCompilerNodeVisitorInterface[] $nodeVisitors
     */
    public function __construct(SmartFileSystem $smartFileSystem, Parser $latteParser, UnknownMacroAwareLatteCompiler $unknownMacroAwareLatteCompiler, SimpleNameResolver $simpleNameResolver, Standard $printerStandard, LineCommentCorrector $lineCommentCorrector, LatteVarTypeDocBlockDecorator $latteVarTypeDocBlockDecorator, array $nodeVisitors)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->latteParser = $latteParser;
        $this->unknownMacroAwareLatteCompiler = $unknownMacroAwareLatteCompiler;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->printerStandard = $printerStandard;
        $this->lineCommentCorrector = $lineCommentCorrector;
        $this->latteVarTypeDocBlockDecorator = $latteVarTypeDocBlockDecorator;
        $this->nodeVisitors = $nodeVisitors;
    }
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param ComponentNameAndType[] $componentNamesAndtTypes
     */
    public function compileContent(string $templateFileContent, array $variablesAndTypes, array $componentNamesAndtTypes) : string
    {
        $this->ensureIsNotFilePath($templateFileContent);
        $latteTokens = $this->latteParser->parse($templateFileContent);
        $rawPhpContent = $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');
        $rawPhpContent = $this->lineCommentCorrector->correctLineNumberPosition($rawPhpContent);
        $phpStmts = $this->parsePhpContentToPhpStmts($rawPhpContent);
        $this->decorateStmts($phpStmts, $componentNamesAndtTypes);
        $phpContent = $this->printerStandard->prettyPrintFile($phpStmts);
        return $this->latteVarTypeDocBlockDecorator->decorateLatteContentWithTypes($phpContent, $variablesAndTypes);
    }
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    public function compileFilePath(string $templateFilePath, array $variablesAndTypes, array $componentNamesAndTypes) : string
    {
        $templateFileContent = $this->smartFileSystem->readFile($templateFilePath);
        return $this->compileContent($templateFileContent, $variablesAndTypes, $componentNamesAndTypes);
    }
    /**
     * @return Stmt[]
     */
    private function parsePhpContentToPhpStmts(string $rawPhpContent) : array
    {
        $parserFactory = new ParserFactory();
        $phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        return (array) $phpParser->parse($rawPhpContent);
    }
    /**
     * @param Stmt[] $phpStmts
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    private function decorateStmts(array $phpStmts, array $componentNamesAndTypes) : void
    {
        $nodeTraverser = new NodeTraverser();
        $controlRenderToExplicitCallNodeVisitor = new ControlRenderToExplicitCallNodeVisitor($this->simpleNameResolver, $componentNamesAndTypes);
        $nodeTraverser->addVisitor($controlRenderToExplicitCallNodeVisitor);
        foreach ($this->nodeVisitors as $nodeVisitor) {
            $nodeTraverser->addVisitor($nodeVisitor);
        }
        $nodeTraverser->traverse($phpStmts);
    }
    private function ensureIsNotFilePath(string $templateFileContent) : void
    {
        if (!\file_exists($templateFileContent)) {
            return;
        }
        $errorMessage = \sprintf('The file path "%s" was passed as 1st argument in "%s()" metohd. Must be file content instead.', $templateFileContent, __METHOD__);
        throw new ShouldNotHappenException($errorMessage);
    }
}
/**
 * @see \Reveal\LattePHPStanCompiler\Tests\LatteToPhpCompiler\LatteToPhpCompilerTest
 */
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\LatteToPhpCompiler', 'Reveal\\LattePHPStanCompiler\\LatteToPhpCompiler', \false);

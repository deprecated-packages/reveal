<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Reveal\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use Reveal\TwigPHPStanCompiler\Contract\NodeVisitor\NormalizingNodeVisitorInterface;
use Reveal\TwigPHPStanCompiler\DocBlock\NonVarTypeDocBlockCleaner;
use Reveal\TwigPHPStanCompiler\ErrorReporting\TemplateLinesMapResolver;
use Reveal\TwigPHPStanCompiler\Exception\TwigPHPStanCompilerException;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\CollectForeachedVariablesNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\ExpandForeachContextNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\ExtractDoDisplayStmtsNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\RemoveUselessClassMethodsNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\ReplaceEchoWithVarDocTypeNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\TwigGetAttributeExpanderNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapCoalesceContextNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapContextVariableNodeVisitor;
use Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapTwigEnsureTraversableNodeVisitor;
use Reveal\TwigPHPStanCompiler\Reflection\PublicPropertyAnalyzer;
use Reveal\TwigPHPStanCompiler\Twig\TolerantTwigEnvironment;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\SmartFileSystem\SmartFileSystem;
use RevealPrefix20220820\Twig\Lexer;
use RevealPrefix20220820\Twig\Loader\ArrayLoader;
use RevealPrefix20220820\Twig\Node\ModuleNode;
use RevealPrefix20220820\Twig\Node\Node;
use RevealPrefix20220820\Twig\Source;
/**
 * @see \Reveal\TwigPHPStanCompiler\Tests\TwigToPhpCompiler\TwigToPhpCompilerTest
 */
final class TwigToPhpCompiler
{
    /**
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;
    /**
     * @var \Reveal\TwigPHPStanCompiler\TwigVarTypeDocBlockDecorator
     */
    private $twigVarTypeDocBlockDecorator;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\TwigPHPStanCompiler\ObjectTypeMethodAnalyzer
     */
    private $objectTypeMethodAnalyzer;
    /**
     * @var \Reveal\TwigPHPStanCompiler\Reflection\PublicPropertyAnalyzer
     */
    private $publicPropertyAnalyzer;
    /**
     * @var \Reveal\TwigPHPStanCompiler\ErrorReporting\TemplateLinesMapResolver
     */
    private $templateLinesMapResolver;
    /**
     * @var \Reveal\TwigPHPStanCompiler\DocBlock\NonVarTypeDocBlockCleaner
     */
    private $nonVarTypeDocBlockCleaner;
    /**
     * @var \Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor\ExtractDoDisplayStmtsNodeVisitor
     */
    private $extractDoDisplayStmtsNodeVisitor;
    /**
     * @var NormalizingNodeVisitorInterface[]
     */
    private $normalizingNodeVisitors;
    /**
     * @param NormalizingNodeVisitorInterface[] $normalizingNodeVisitors
     */
    public function __construct(SmartFileSystem $smartFileSystem, Standard $printerStandard, \Reveal\TwigPHPStanCompiler\TwigVarTypeDocBlockDecorator $twigVarTypeDocBlockDecorator, SimpleNameResolver $simpleNameResolver, \Reveal\TwigPHPStanCompiler\ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer, PublicPropertyAnalyzer $publicPropertyAnalyzer, TemplateLinesMapResolver $templateLinesMapResolver, NonVarTypeDocBlockCleaner $nonVarTypeDocBlockCleaner, ExtractDoDisplayStmtsNodeVisitor $extractDoDisplayStmtsNodeVisitor, array $normalizingNodeVisitors)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->printerStandard = $printerStandard;
        $this->twigVarTypeDocBlockDecorator = $twigVarTypeDocBlockDecorator;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->objectTypeMethodAnalyzer = $objectTypeMethodAnalyzer;
        $this->publicPropertyAnalyzer = $publicPropertyAnalyzer;
        $this->templateLinesMapResolver = $templateLinesMapResolver;
        $this->nonVarTypeDocBlockCleaner = $nonVarTypeDocBlockCleaner;
        $this->extractDoDisplayStmtsNodeVisitor = $extractDoDisplayStmtsNodeVisitor;
        $this->normalizingNodeVisitors = $normalizingNodeVisitors;
        // avoids unneeded caching from phpstan parser, we need to change content of same file based on provided variable types
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }
    /**
     * @param array<VariableAndType> $variablesAndTypes
     */
    public function compileContent(string $filePath, array $variablesAndTypes) : PhpFileContentsWithLineMap
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $tolerantTwigEnvironment = $this->createTwigEnvironment($filePath, $fileContent);
        $moduleNode = $this->parseFileContentToModuleNode($tolerantTwigEnvironment, $fileContent, $filePath);
        $rawPhpContent = $tolerantTwigEnvironment->compile($moduleNode);
        $decoratedPhpContent = $this->decoratePhpContent($rawPhpContent, $variablesAndTypes);
        $phpLinesToTemplateLines = $this->templateLinesMapResolver->resolve($decoratedPhpContent);
        return new PhpFileContentsWithLineMap($decoratedPhpContent, $phpLinesToTemplateLines);
    }
    private function createTwigEnvironment(string $filePath, string $fileContent) : TolerantTwigEnvironment
    {
        $arrayLoader = new ArrayLoader([$filePath => $fileContent]);
        return new TolerantTwigEnvironment($arrayLoader);
    }
    /**
     * @return ModuleNode<Node>
     */
    private function parseFileContentToModuleNode(TolerantTwigEnvironment $tolerantTwigEnvironment, string $fileContent, string $filePath) : ModuleNode
    {
        // this should disable comments as we know it - if we don't change it here, the tokenizer will remove all comments completely
        $lexer = new Lexer($tolerantTwigEnvironment, ['tag_comment' => ['{*', '*}']]);
        $tolerantTwigEnvironment->setLexer($lexer);
        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));
        $clearTokenStream = $this->nonVarTypeDocBlockCleaner->cleanTokenStream($tokenStream);
        return $tolerantTwigEnvironment->parse($clearTokenStream);
    }
    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    private function decoratePhpContent(string $phpContent, array $variablesAndTypes) : string
    {
        $stmts = $this->parser->parse($phpContent);
        if ($stmts === null) {
            throw new TwigPHPStanCompilerException();
        }
        // -1. remove useless class methods
        $removeUselessClassMethodsNodeVisitor = new RemoveUselessClassMethodsNodeVisitor();
        $this->traverseStmtsWithVisitors($stmts, [$removeUselessClassMethodsNodeVisitor]);
        // 0. add types first?
        $this->unwarpMagicVariables($stmts);
        // 1. hacking {# @var variable type #} comments to /** @var types */
        $replaceEchoWithVarDocTypeNodeVisitor = new ReplaceEchoWithVarDocTypeNodeVisitor();
        $this->traverseStmtsWithVisitors($stmts, [$replaceEchoWithVarDocTypeNodeVisitor]);
        // get those types for further analysis
        $collectedVariablesAndTypes = $replaceEchoWithVarDocTypeNodeVisitor->getCollectedVariablesAndTypes();
        $variablesAndTypes = \array_merge($variablesAndTypes, $collectedVariablesAndTypes);
        // 3. collect foreached variables to determine nested value :)
        $collectForeachedVariablesNodeVisitor = new CollectForeachedVariablesNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$collectForeachedVariablesNodeVisitor]);
        // 2. replace twig_get_attribute with direct access/call
        $twigGetAttributeExpanderNodeVisitor = new TwigGetAttributeExpanderNodeVisitor($this->simpleNameResolver, $this->objectTypeMethodAnalyzer, $this->publicPropertyAnalyzer, $variablesAndTypes, $collectForeachedVariablesNodeVisitor->getForeachedVariablesToSingles());
        $this->traverseStmtsWithVisitors($stmts, [$twigGetAttributeExpanderNodeVisitor]);
        foreach ($this->normalizingNodeVisitors as $normalizingNodeVisitor) {
            $this->traverseStmtsWithVisitors($stmts, [$normalizingNodeVisitor]);
        }
        // get do display method contents
        $phpContent = $this->printerStandard->prettyPrintFile($stmts);
        $fileContent = $this->twigVarTypeDocBlockDecorator->decorateTwigContentWithTypes($phpContent, $variablesAndTypes);
        $stmts = $this->parser->parse($fileContent);
        if ($stmts === null) {
            throw new TwigPHPStanCompilerException();
        }
        $stmts = $this->extractDoDisplayStmts($stmts);
        $printerStandard = new Standard();
        return $printerStandard->prettyPrintFile($stmts) . \PHP_EOL;
    }
    /**
     * @param Stmt[] $stmts
     * @param NodeVisitor[] $nodeVisitors
     */
    private function traverseStmtsWithVisitors(array $stmts, array $nodeVisitors) : void
    {
        $nodeTraverser = new NodeTraverser();
        foreach ($nodeVisitors as $nodeVisitor) {
            $nodeTraverser->addVisitor($nodeVisitor);
        }
        $nodeTraverser->traverse($stmts);
    }
    /**
     * @param Stmt[] $stmts
     */
    private function unwarpMagicVariables(array $stmts) : void
    {
        // 1. run $context unwrap first, as needed everywhere
        $unwrapContextVariableNodeVisitor = new UnwrapContextVariableNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$unwrapContextVariableNodeVisitor]);
        // 2. unwrap coalesce $context
        $unwrapCoalesceContextNodeVisitor = new UnwrapCoalesceContextNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$unwrapCoalesceContextNodeVisitor]);
        // 3. unwrap twig_ensure_traversable()
        $unwrapTwigEnsureTraversableNodeVisitor = new UnwrapTwigEnsureTraversableNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$unwrapTwigEnsureTraversableNodeVisitor]);
        // 4. expand foreached magic to make type references clear for iterated variables
        $expandForeachContextNodeVisitor = new ExpandForeachContextNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$expandForeachContextNodeVisitor]);
    }
    /**
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    private function extractDoDisplayStmts(array $stmts) : array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->extractDoDisplayStmtsNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $this->extractDoDisplayStmtsNodeVisitor->getDoDisplayStmts();
    }
}

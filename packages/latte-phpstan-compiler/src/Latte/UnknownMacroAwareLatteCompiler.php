<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte;

use RevealPrefix20220606\Latte\CompileException;
use RevealPrefix20220606\Latte\Compiler;
use RevealPrefix20220606\Latte\HtmlNode;
use RevealPrefix20220606\Latte\MacroNode;
use RevealPrefix20220606\Latte\Macros\BlockMacros;
use RevealPrefix20220606\Latte\Macros\CoreMacros;
use RevealPrefix20220606\Latte\Runtime\Defaults;
use RevealPrefix20220606\Latte\Token;
use RevealPrefix20220606\Nette\Bridges\ApplicationLatte\UIMacros;
use RevealPrefix20220606\Nette\Bridges\FormsLatte\FormMacros;
use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\Macros\LatteMacroFaker;
use RevealPrefix20220606\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
final class UnknownMacroAwareLatteCompiler extends Compiler
{
    /**
     * @var string
     * @see https://regex101.com/r/bjXNkN/1
     */
    private const MISSING_MACRO_REGEX = '#^Unexpected {\\/(?<macro_name>\\w+)}#';
    /**
     * @var string[]
     */
    private $nativeMacrosNames = [];
    /**
     * @var string[]
     */
    private $endRequiringMacroNames = [];
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesAccessor
     */
    private $privatesAccessor;
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\Macros\LatteMacroFaker
     */
    private $latteMacroFaker;
    public function __construct(PrivatesAccessor $privatesAccessor, LatteMacroFaker $latteMacroFaker)
    {
        $this->privatesAccessor = $privatesAccessor;
        $this->latteMacroFaker = $latteMacroFaker;
        $this->installDefaultMacros($this);
        $runtimeDefaults = new Defaults();
        $functionNames = \array_keys($runtimeDefaults->getFunctions());
        $this->setFunctions($functionNames);
        /** @var array<string, mixed> $macros */
        $macros = $this->privatesAccessor->getPrivateProperty($this, 'macros');
        $this->nativeMacrosNames = \array_keys($macros);
        \sort($this->nativeMacrosNames);
    }
    /**
     * @override
     */
    public function expandMacro(string $name, string $args, string $modifiers = '', string $nPrefix = null) : MacroNode
    {
        // missing macro!
        if (!\in_array($name, $this->nativeMacrosNames, \true)) {
            $this->latteMacroFaker->fakeMacro($this, $name, $this->endRequiringMacroNames);
        }
        return parent::expandMacro($name, $args, $modifiers, $nPrefix);
    }
    /**
     * @param Token[] $tokens
     */
    public function compile(array $tokens, string $className, string $comment = null, bool $strictMode = \true) : string
    {
        // @todo compile loop counter?
        try {
            return parent::compile($tokens, $className, $className, $strictMode);
        } catch (CompileException $compileException) {
            // potential pair macro detection
            $match = Strings::match($compileException->getMessage(), self::MISSING_MACRO_REGEX);
            // nothing found, just fail
            if (!isset($match['macro_name'])) {
                throw $compileException;
            }
            // mark the dual macro tag and re-try compiling
            $this->endRequiringMacroNames[] = $match['macro_name'];
            return $this->compile($tokens, $className, $comment, $strictMode);
        }
    }
    /**
     * Generates code for macro <tag n:attr> to the output.
     *
     * @internal
     * @override
     */
    public function writeAttrsMacro(string $html, ?bool $empty = null) : void
    {
        $htmlNode = $this->privatesAccessor->getPrivatePropertyOfClass($this, 'htmlNode', HtmlNode::class);
        // all collected n:attributes with nodes
        $attrs = $htmlNode->macroAttrs;
        foreach (\array_keys($attrs) as $macroName) {
            $this->latteMacroFaker->fakeAttrMacro($this, $this->nativeMacrosNames, $macroName);
        }
        parent::writeAttrsMacro($html, $empty);
    }
    private function installDefaultMacros(self $compiler) : void
    {
        // make sure basic macros are installed
        CoreMacros::install($compiler);
        BlockMacros::install($compiler);
        if (\class_exists('RevealPrefix20220606\\Nette\\Bridges\\ApplicationLatte\\UIMacros')) {
            UIMacros::install($compiler);
        }
        if (\class_exists('RevealPrefix20220606\\Nette\\Bridges\\FormsLatte\\FormMacros')) {
            FormMacros::install($compiler);
        }
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\Latte\\UnknownMacroAwareLatteCompiler', 'Reveal\\LattePHPStanCompiler\\Latte\\UnknownMacroAwareLatteCompiler', \false);

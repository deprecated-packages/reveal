<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler;

use RevealPrefix20220606\Nette\Application\UI\Control;
use RevealPrefix20220606\PhpParser\Node\Expr\Array_;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\PHPStan\Reflection\ClassReflection;
use RevealPrefix20220606\PHPStan\Type\ObjectType;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Latte\Tokens\PhpToLatteLineNumbersResolver;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\TypeAnalyzer\TemplateVariableTypesResolver;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
/**
 * @api
 */
final class TemplateFileVarTypeDocBlocksDecorator
{
    /**
     * @var \Reveal\LattePHPStanCompiler\LatteToPhpCompiler
     */
    private $latteToPhpCompiler;
    /**
     * @var \Reveal\LattePHPStanCompiler\Latte\Tokens\PhpToLatteLineNumbersResolver
     */
    private $phpToLatteLineNumbersResolver;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\TypeAnalyzer\TemplateVariableTypesResolver
     */
    private $templateVariableTypesResolver;
    /**
     * @var LatteVariableCollectorInterface[]
     */
    private $latteVariableCollectors;
    /**
     * @param LatteVariableCollectorInterface[] $latteVariableCollectors
     */
    public function __construct(LatteToPhpCompiler $latteToPhpCompiler, PhpToLatteLineNumbersResolver $phpToLatteLineNumbersResolver, TemplateVariableTypesResolver $templateVariableTypesResolver, array $latteVariableCollectors)
    {
        $this->latteToPhpCompiler = $latteToPhpCompiler;
        $this->phpToLatteLineNumbersResolver = $phpToLatteLineNumbersResolver;
        $this->templateVariableTypesResolver = $templateVariableTypesResolver;
        $this->latteVariableCollectors = $latteVariableCollectors;
    }
    /**
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    public function decorate(string $latteFilePath, Array_ $array, Scope $scope, array $componentNamesAndTypes) : PhpFileContentsWithLineMap
    {
        $variablesAndTypes = $this->resolveLatteVariablesAndTypes($array, $scope);
        $phpContent = $this->latteToPhpCompiler->compileFilePath($latteFilePath, $variablesAndTypes, $componentNamesAndTypes);
        $phpLinesToLatteLines = $this->phpToLatteLineNumbersResolver->resolve($phpContent);
        return new PhpFileContentsWithLineMap($phpContent, $phpLinesToLatteLines);
    }
    /**
     * @return VariableAndType[]
     */
    private function resolveLatteVariablesAndTypes(Array_ $array, Scope $scope) : array
    {
        // traverse nodes to add types after \DummyTemplateClass::main()
        $variablesAndTypes = $this->templateVariableTypesResolver->resolveArray($array, $scope);
        foreach ($this->latteVariableCollectors as $latteVariableCollector) {
            $collectedVariablesAndTypes = $latteVariableCollector->getVariablesAndTypes();
            $variablesAndTypes = \array_merge($variablesAndTypes, $collectedVariablesAndTypes);
        }
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection) {
            $objectType = new ObjectType($classReflection->getName());
            $variablesAndTypes[] = new VariableAndType('actualClass', $objectType);
            if ($objectType->isInstanceOf('RevealPrefix20220606\\Nette\\Application\\UI\\Presenter')->yes()) {
                $variablesAndTypes[] = new VariableAndType('presenter', $objectType);
            }
            if ($objectType->isInstanceOf(Control::class)->yes()) {
                $variablesAndTypes[] = new VariableAndType('control', $objectType);
            }
        }
        return $variablesAndTypes;
    }
}
/**
 * @api
 */
\class_alias('RevealPrefix20220606\\Reveal\\LattePHPStanCompiler\\TemplateFileVarTypeDocBlocksDecorator', 'Reveal\\LattePHPStanCompiler\\TemplateFileVarTypeDocBlocksDecorator', \false);

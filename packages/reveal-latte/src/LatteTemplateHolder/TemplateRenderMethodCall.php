<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\LatteTemplateHolder;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use RevealPrefix20220606\Reveal\RevealLatte\Contract\LatteTemplateHolderInterface;
use RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer\LatteTemplateWithParametersMatcher;
use RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer\TemplateRenderAnalyzer;
use RevealPrefix20220606\Reveal\RevealLatte\TypeAnalyzer\ComponentMapResolver;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
final class TemplateRenderMethodCall implements LatteTemplateHolderInterface
{
    /**
     * @var \Reveal\RevealLatte\NodeAnalyzer\TemplateRenderAnalyzer
     */
    private $templateRenderAnalyzer;
    /**
     * @var \Reveal\RevealLatte\NodeAnalyzer\LatteTemplateWithParametersMatcher
     */
    private $latteTemplateWithParametersMatcher;
    /**
     * @var \Reveal\RevealLatte\TypeAnalyzer\ComponentMapResolver
     */
    private $componentMapResolver;
    public function __construct(TemplateRenderAnalyzer $templateRenderAnalyzer, LatteTemplateWithParametersMatcher $latteTemplateWithParametersMatcher, ComponentMapResolver $componentMapResolver)
    {
        $this->templateRenderAnalyzer = $templateRenderAnalyzer;
        $this->latteTemplateWithParametersMatcher = $latteTemplateWithParametersMatcher;
        $this->componentMapResolver = $componentMapResolver;
    }
    public function check(Node $node, Scope $scope) : bool
    {
        if (!$node instanceof MethodCall) {
            return \false;
        }
        return $this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope);
    }
    /**
     * @param MethodCall $node
     * @return RenderTemplateWithParameters[]
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope) : array
    {
        return $this->latteTemplateWithParametersMatcher->match($node, $scope);
    }
    /**
     * @param MethodCall $node
     * @return ComponentNameAndType[]
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope) : array
    {
        return $this->componentMapResolver->resolveFromMethodCall($node, $scope);
    }
}

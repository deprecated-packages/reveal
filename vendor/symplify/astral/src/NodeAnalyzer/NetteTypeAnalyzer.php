<?php

declare (strict_types=1);
namespace RevealPrefix20220711\Symplify\Astral\NodeAnalyzer;

use RevealPrefix20220711\Nette\Application\UI\Template;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use RevealPrefix20220711\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220711\Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser;
/**
 * @api
 */
final class NetteTypeAnalyzer
{
    /**
     * @var array<class-string<Template>>
     */
    private const TEMPLATE_TYPES = ['RevealPrefix20220711\\Nette\\Application\\UI\\Template', 'RevealPrefix20220711\\Nette\\Application\\UI\\ITemplate', 'RevealPrefix20220711\\Nette\\Bridges\\ApplicationLatte\\Template', 'RevealPrefix20220711\\Nette\\Bridges\\ApplicationLatte\\DefaultTemplate'];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;
    public function __construct(SimpleNameResolver $simpleNameResolver, ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->containsTypeAnalyser = $containsTypeAnalyser;
    }
    /**
     * E.g. $this->template->key
     */
    public function isTemplateMagicPropertyType(Expr $expr, Scope $scope) : bool
    {
        if (!$expr instanceof PropertyFetch) {
            return \false;
        }
        if (!$expr->var instanceof PropertyFetch) {
            return \false;
        }
        return $this->isTemplateType($expr->var, $scope);
    }
    /**
     * E.g. $this->template
     */
    public function isTemplateType(Expr $expr, Scope $scope) : bool
    {
        return $this->containsTypeAnalyser->containsExprTypes($expr, $scope, self::TEMPLATE_TYPES);
    }
    /**
     * This type has getComponent() method
     */
    public function isInsideComponentContainer(Scope $scope) : bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return \false;
        }
        // this type has getComponent() method
        return \is_a($className, 'RevealPrefix20220711\\Nette\\ComponentModel\\Container', \true);
    }
    public function isInsideControl(Scope $scope) : bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return \false;
        }
        return \is_a($className, 'RevealPrefix20220711\\Nette\\Application\\UI\\Control', \true);
    }
}

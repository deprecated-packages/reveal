<?php

declare (strict_types=1);
namespace Reveal\RevealTwig\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Reveal\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
use RevealPrefix20220820\Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use RevealPrefix20220820\Symfony\Component\HttpFoundation\Response;
use Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220820\Twig\Environment;
final class SymfonyRenderWithParametersMatcher
{
    /**
     * @var string
     */
    private const RENDER = 'render';
    /**
     * @var string[]
     */
    private const RENDER_METHOD_NAMES = [self::RENDER, 'renderView'];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\RevealTwig\NodeAnalyzer\TwigRenderTemplateWithParametersMatcher
     */
    private $twigRenderTemplateWithParametersMatcher;
    public function __construct(SimpleNameResolver $simpleNameResolver, \Reveal\RevealTwig\NodeAnalyzer\TwigRenderTemplateWithParametersMatcher $twigRenderTemplateWithParametersMatcher)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->twigRenderTemplateWithParametersMatcher = $twigRenderTemplateWithParametersMatcher;
    }
    /**
     * @return RenderTemplateWithParameters[]
     */
    public function matchSymfonyRender(MethodCall $methodCall, Scope $scope) : array
    {
        if (!$this->simpleNameResolver->isNames($methodCall->name, self::RENDER_METHOD_NAMES)) {
            return [];
        }
        $methodCallReturnType = $scope->getType($methodCall);
        if (!$methodCallReturnType instanceof ObjectType) {
            return [];
        }
        if (!$methodCallReturnType->isInstanceOf(Response::class)->yes()) {
            return [];
        }
        return $this->twigRenderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
    }
    /**
     * @return RenderTemplateWithParameters[]
     */
    public function matchTwigRender(MethodCall $methodCall, Scope $scope) : array
    {
        $callerType = $scope->getType($methodCall->var);
        if ($callerType instanceof ThisType) {
            $callerType = new ObjectType($callerType->getClassName());
        }
        if (!$callerType instanceof ObjectType) {
            return [];
        }
        if (!$this->isTwigCallerType($callerType, $methodCall)) {
            return [];
        }
        return $this->twigRenderTemplateWithParametersMatcher->match($methodCall, $scope, 'twig');
    }
    private function isTwigCallerType(ObjectType $objectType, MethodCall $methodCall) : bool
    {
        if ($objectType->isInstanceOf(Environment::class)->yes()) {
            return $this->simpleNameResolver->isName($methodCall->name, self::RENDER);
        }
        if ($objectType->isInstanceOf(AbstractController::class)->yes()) {
            return $this->simpleNameResolver->isNames($methodCall->name, [self::RENDER, 'renderView']);
        }
        return \false;
    }
}

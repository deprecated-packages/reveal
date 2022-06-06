<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\LinkProcessor;

use RevealPrefix20220606\PhpParser\Comment\Doc;
use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use Reveal\LattePHPStanCompiler\Contract\LinkProcessorInterface;
use Reveal\LattePHPStanCompiler\Nette\PresenterFactoryFaker;
/**
 * from: <code> echo \Latte\Runtime\Filters::escapeHtmlAttr($this->global->uiControl->link("Foo:doSomething", ['a']));
 * </code>
 *
 * to: <code> $fooPresenter->actionDoSomething('a'); $fooPresenter->renderDoSomething('a'); </code>
 */
final class PresenterActionLinkProcessor implements LinkProcessorInterface
{
    /**
     * @var \Reveal\LattePHPStanCompiler\Nette\PresenterFactoryFaker
     */
    private $presenterFactoryFaker;
    public function __construct(PresenterFactoryFaker $presenterFactoryFaker)
    {
        $this->presenterFactoryFaker = $presenterFactoryFaker;
    }
    public function check(string $targetName) : bool
    {
        return \strpos($targetName, ':') !== \false;
    }
    /**
     * @param Arg[] $linkParams
     * @param array<string, mixed> $attributes
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes) : array
    {
        $actionParts = \explode(':', $targetName);
        $actionName = \array_pop($actionParts);
        $presenterName = \implode('', $actionParts);
        $presenterVariableName = \lcfirst($presenterName) . 'Presenter';
        $presenterFactory = $this->presenterFactoryFaker->getPresenterFactory();
        $presenterClassName = $presenterFactory->formatPresenterClass($presenterName);
        $variable = new Variable($presenterVariableName);
        $methodNames = $this->prepareMethodNames($presenterClassName, $actionName);
        $attributes['comments'][] = new Doc('/** @var ' . $presenterClassName . ' $' . $presenterVariableName . ' */');
        $expressions = [];
        foreach ($methodNames as $methodName) {
            $expressions[] = new Expression(new MethodCall($variable, $methodName, $linkParams), $attributes);
            $attributes = [];
            // reset attributes, we want to print them only with first expression
        }
        return $expressions;
    }
    /**
     * @return string[]
     */
    private function prepareMethodNames(string $presenterClassName, string $actionName) : array
    {
        $methodNames = [];
        // both methods have to have same parameters, so we check them both if exist
        foreach (['action', 'render'] as $type) {
            $methodName = $type . \ucfirst($actionName);
            if (\method_exists($presenterClassName, $methodName)) {
                $methodNames[] = $methodName;
            }
        }
        return $methodNames;
    }
}

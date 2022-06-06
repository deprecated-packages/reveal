<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\LinkProcessor;

use RevealPrefix20220606\PhpParser\Node\Arg;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PhpParser\Node\Expr\Variable;
use RevealPrefix20220606\PhpParser\Node\Stmt\Expression;
use Reveal\LattePHPStanCompiler\Contract\LinkProcessorInterface;
/**
 * from: <code> echo \Latte\Runtime\Filters::escapeHtmlAttr($this->global->uiControl->link("doSomething!", ['a']));
 * </code>
 *
 * to: <code> $actualClass->handleDoSomething('a'); </code>
 */
final class SignalLinkProcessor implements LinkProcessorInterface
{
    public function check(string $targetName) : bool
    {
        return \substr_compare($targetName, '!', -\strlen('!')) === 0;
    }
    /**
     * @param Arg[] $linkParams
     * @param array<string, mixed> $attributes
     * @return Expression[]
     */
    public function createLinkExpressions(string $targetName, array $linkParams, array $attributes) : array
    {
        $variable = new Variable('actualClass');
        $methodName = 'handle' . \ucfirst(\substr($targetName, 0, -1));
        return [new Expression(new MethodCall($variable, $methodName, $linkParams), $attributes)];
    }
}

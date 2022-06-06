<?php

declare (strict_types=1);
namespace Reveal\RevealLatte\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer;
final class TemplateRenderAnalyzer
{
    /**
     * @var string[]
     */
    private const NETTE_RENDER_METHOD_NAMES = ['render', 'renderToString', 'action'];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\NodeAnalyzer\NetteTypeAnalyzer
     */
    private $netteTypeAnalyzer;
    public function __construct(SimpleNameResolver $simpleNameResolver, NetteTypeAnalyzer $netteTypeAnalyzer)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->netteTypeAnalyzer = $netteTypeAnalyzer;
    }
    public function isNetteTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope) : bool
    {
        if (!$this->simpleNameResolver->isNames($methodCall->name, self::NETTE_RENDER_METHOD_NAMES)) {
            return \false;
        }
        return $this->netteTypeAnalyzer->isTemplateType($methodCall->var, $scope);
    }
}

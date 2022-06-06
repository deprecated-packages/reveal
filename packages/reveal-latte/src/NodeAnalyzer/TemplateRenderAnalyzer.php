<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\NodeAnalyzer;

use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
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
\class_alias('RevealPrefix20220606\\Reveal\\RevealLatte\\NodeAnalyzer\\TemplateRenderAnalyzer', 'Reveal\\RevealLatte\\NodeAnalyzer\\TemplateRenderAnalyzer', \false);

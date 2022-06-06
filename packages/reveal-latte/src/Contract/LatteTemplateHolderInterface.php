<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealLatte\Contract;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;
interface LatteTemplateHolderInterface
{
    /**
     * call before other methods
     */
    public function check(Node $node, Scope $scope) : bool;
    /**
     * @return RenderTemplateWithParameters[]
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope) : array;
    /**
     * @return ComponentNameAndType[]
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope) : array;
}

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node;

use RevealPrefix20220606\PhpParser\NodeAbstract;
/**
 * Represents the "..." in "foo(...)" of the first-class callable syntax.
 */
class VariadicPlaceholder extends NodeAbstract
{
    /**
     * Create a variadic argument placeholder (first-class callable syntax).
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
    public function getType() : string
    {
        return 'VariadicPlaceholder';
    }
    public function getSubNodeNames() : array
    {
        return [];
    }
}
/**
 * Represents the "..." in "foo(...)" of the first-class callable syntax.
 */
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\VariadicPlaceholder', 'PhpParser\\Node\\VariadicPlaceholder', \false);

<?php

declare (strict_types=1);
namespace RevealPrefix20220606\PhpParser\Node;

use RevealPrefix20220606\PhpParser\NodeAbstract;
class IntersectionType extends ComplexType
{
    /** @var (Identifier|Name)[] Types */
    public $types;
    /**
     * Constructs an intersection type.
     *
     * @param (Identifier|Name)[] $types      Types
     * @param array               $attributes Additional attributes
     */
    public function __construct(array $types, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->types = $types;
    }
    public function getSubNodeNames() : array
    {
        return ['types'];
    }
    public function getType() : string
    {
        return 'IntersectionType';
    }
}
\class_alias('RevealPrefix20220606\\PhpParser\\Node\\IntersectionType', 'PhpParser\\Node\\IntersectionType', \false);

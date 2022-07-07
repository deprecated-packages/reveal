<?php

declare (strict_types=1);
namespace RevealPrefix20220707\Symplify\Astral\StaticFactory;

use RevealPrefix20220707\Symplify\Astral\NodeValue\NodeValueResolver;
use RevealPrefix20220707\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : NodeValueResolver
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        return new NodeValueResolver($simpleNameResolver, new TypeChecker());
    }
}

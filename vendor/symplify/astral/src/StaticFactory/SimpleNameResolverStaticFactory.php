<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\StaticFactory;

use RevealPrefix20220606\Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\ArgNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\AttributeNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\ClassLikeNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\ClassMethodNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\ConstFetchNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\FuncCallNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\IdentifierNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\NamespaceNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\ParamNodeNameResolver;
use RevealPrefix20220606\Symplify\Astral\NodeNameResolver\PropertyNodeNameResolver;
/**
 * This would be normally handled by standard Symfony or Nette DI, but PHPStan does not use any of those, so we have to
 * make it manually.
 */
final class SimpleNameResolverStaticFactory
{
    public static function create() : SimpleNameResolver
    {
        $nameResolvers = [new ArgNodeNameResolver(), new AttributeNodeNameResolver(), new ClassLikeNodeNameResolver(), new ClassMethodNodeNameResolver(), new ConstFetchNodeNameResolver(), new FuncCallNodeNameResolver(), new IdentifierNodeNameResolver(), new NamespaceNodeNameResolver(), new ParamNodeNameResolver(), new PropertyNodeNameResolver()];
        return new SimpleNameResolver($nameResolvers);
    }
}

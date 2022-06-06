<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\Astral\Contract;

use RevealPrefix20220606\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(Node $node) : bool;
    public function resolve(Node $node) : ?string;
}

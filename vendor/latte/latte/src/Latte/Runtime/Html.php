<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace RevealPrefix20220705\Latte\Runtime;

use RevealPrefix20220705\Latte;
/**
 * HTML literal.
 */
class Html implements HtmlStringable
{
    use Latte\Strict;
    /** @var string */
    private $value;
    public function __construct($value)
    {
        $this->value = (string) $value;
    }
    public function __toString() : string
    {
        return $this->value;
    }
}

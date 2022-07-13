<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RevealPrefix20220713\Symfony\Contracts\Service\Attribute;

use RevealPrefix20220713\Symfony\Contracts\Service\ServiceSubscriberTrait;
/**
 * Use with {@see ServiceSubscriberTrait} to mark a method's return type
 * as a subscribed service.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class SubscribedService
{
    /**
     * @var string|null
     */
    public $key;
    /**
     * @param string|null $key The key to use for the service
     *                         If null, use "ClassName::methodName"
     */
    public function __construct(?string $key = null)
    {
        $this->key = $key;
    }
}

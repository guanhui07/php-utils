<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2019 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace Raylin666\Utils\Traits;

use BadMethodCallException;
use Error;
use function get_class;

/**
 * Trait ForwardsCalls
 * @package Raylin666\Utils\Traits
 */
trait ForwardsCalls
{
    /**
     * Forward a method call to the given object.
     *
     * @param mixed $object
     * @throws Error
     */
    protected function forwardCallTo($object, string $method, array $parameters)
    {
        try {
            return $object->{$method}(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            $pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';

            if (! preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }

            if ($matches['class'] !== get_class($object) || $matches['method'] !== $method) {
                throw $e;
            }

            static::throwBadMethodCallException($method);
        }
    }

    /**
     * Throw a bad method call exception for the given method.
     * @throws BadMethodCallException
     */
    protected static function throwBadMethodCallException(string $method): void
    {
        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
    }
}

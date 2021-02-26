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

/**
 * Trait Container
 * @package Raylin666\Utils\Traits
 */
trait Container
{
    /**
     * @var array
     */
    protected static $container = [];

    /**
     * Add a value to container by identifier.
     * @param mixed $value
     */
    public static function add(string $id, $value)
    {
        static::$container[$id] = $value;
    }

    /**
     * Finds an entry of the container by its identifier and returns it,
     * Retunrs $default when does not exists in the container.
     * @param null|mixed $default
     */
    public static function get(string $id, $default = null)
    {
        return static::$container[$id] ?? $default;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     */
    public static function has(string $id): bool
    {
        return array_key_exists($id, static::$container);
    }

    /**
     * @param string $id
     */
    public static function destroy(string $id)
    {
        unset(static::$container[$id]);
    }

    /**
     * Returns the containers.
     */
    public static function all(): array
    {
        return static::$container;
    }

    /**
     * Clear the containers.
     */
    public static function flush(): void
    {
        static::$container = [];
    }
}

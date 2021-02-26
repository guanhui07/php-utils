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

use Raylin666\Utils\Context;

/**
 * Trait StaticInstance
 * @package Raylin666\Utils\Traits
 */
trait StaticInstance
{
    /**
     * @param array $params
     * @param bool  $refresh
     * @return mixed|StaticInstance|null
     */
    public static function instance($params = [], $refresh = false)
    {
        $key = get_called_class();

        $instance = null;
        if (Context::has($key)) {
            $instance = Context::get($key);
        }

        if ($refresh || is_null($instance) || ! $instance instanceof static) {
            $instance = new static(...$params);
            Context::set($key, $instance);
        }

        return $instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.

        return static::instance()->$name(...$arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.

        return static::instance()->$name(...$arguments);
    }
}

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

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use BadMethodCallException;
use Closure;

/**
 * Trait Macroable
 * @package Raylin666\Utils\Traits
 *
 * 用法:
 *      $macros 是创建一个宏指令, Macroable的核心是基于匿名函数的绑定功能。
 *      PHP 可通过匿名函数的绑定功能来扩展类或者实例的功能
 *      (将匿名函数/普通函数/类内某个函数绑定到该$macros静态变量内,之后可直接调用[相当于是该类的函数])。
 *
 *      class A
 *      {
           public function hi()
           {
                return 'emmmm...';
            }
        }

        $filesystem = new \Raylin666\Utils\Helper\Filesystem();
        $filesystem::macro('macroA', [new A(), 'hi']);
        dump($filesystem::macroA());  // 输出 emmmm...
 */
trait Macroable
{
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * Dynamically handle calls to the class.
     *
     * @param string $method
     * @param array $parameters
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        if (static::$macros[$method] instanceof Closure) {
            return call_user_func_array(Closure::bind(static::$macros[$method], null, static::class), $parameters);
        }

        return call_user_func_array(static::$macros[$method], $parameters);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param string $method
     * @param array $parameters
     *
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            return call_user_func_array($macro->bindTo($this, static::class), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }

    /**
     * Register a custom macro.
     *
     * @param string $name
     * @param callable|object $macro
     */
    public static function macro($name, $macro)
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Mix another object into the class.
     *
     * @param object $mixin
     *
     * @throws ReflectionException
     */
    public static function mixin($mixin)
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);

            static::macro($method->name, $method->invoke($mixin));
        }
    }

    /**
     * Checks if macro is registered.
     *
     * @param string $name
     * @return bool
     */
    public static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Delete a $macros element
     * @param $name
     */
    public static function unsetMacro($name)
    {
        unset(self::$macros[$name]);
    }

    /**
     * Clean up all registered $macros elements
     */
    public static function clearMacro()
    {
        self::$macros = [];
    }
}

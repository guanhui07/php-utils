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

namespace Raylin666\Utils\Helper;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class ReflectionHelper
 * @package Raylin666\Utils\Helper
 */
class ReflectionHelper
{
    /**
     * @param $argument
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    public static function doClass($argument): ReflectionClass
    {
        return new ReflectionClass($argument);
    }

    /**
     * @param $name
     * @return ReflectionFunction
     * @throws \ReflectionException
     */
    public static function doFunction($name): ReflectionFunction
    {
        return new ReflectionFunction($name);
    }

    /**
     * @param $class
     * @param $name
     * @return ReflectionMethod
     * @throws \ReflectionException
     */
    public static function doClassMethod($class, $name): ReflectionMethod
    {
        return new ReflectionMethod($class, $name);
    }
}
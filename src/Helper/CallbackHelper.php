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

use Closure;

/**
 * Class CallbackHelper
 * @package Raylin666\Utils\Helper
 */
class CallbackHelper
{
    /**
     * 返回回调值
     * @param $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

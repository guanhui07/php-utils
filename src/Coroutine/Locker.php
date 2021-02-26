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

namespace Raylin666\Utils\Coroutine;

use Raylin666\Utils\Traits\Container;
use Swoole\Coroutine as SwooleCoroutine;

/**
 * Class Locker
 * @package Raylin666\Utils\Coroutine
 */
class Locker
{
    use Container;

    /**
     * @param $key
     * @return bool
     */
    public static function lock($key): bool
    {
        if (! self::has($key)) {
            self::add($key, 0);
            return true;
        }
        self::add($key, Coroutine::id());
        SwooleCoroutine::suspend();
        return false;
    }

    /**
     * @param $key
     */
    public static function unlock($key): void
    {
        if (self::has($key)) {
            $ids = self::get($key);
            foreach ($ids as $id) {
                if ($id > 0) {
                    SwooleCoroutine::resume($id);
                }
            }
            self::destroy($key);
        }
    }
}

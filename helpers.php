<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

use Swoole\Runtime as SwooleRuntime;
use Raylin666\Utils\Helper\PhpHelper;
use Swoole\Coroutine as SwooleCoroutine;
use Raylin666\Utils\ApplicationContext;
use Raylin666\Utils\Coroutine\Coroutine;

if (! function_exists('call'))
{
    /**
     * 调用函数
     * @param       $callback
     * @param array $args
     * @return mixed
     */
    function call($callback, array $args = [])
    {
        return PhpHelper::call($callback, ...$args);
    }
}

if (! function_exists('run'))
{
    /**
     * Run callable in non-coroutine environment, all hook functions by Swoole only available in the callable.
     *
     * @param array|callable $callbacks
     */
    function run($callbacks, $flags = SWOOLE_HOOK_ALL): bool
    {
        if (Coroutine::inCoroutine()) {
            throw new RuntimeException('Function \'run\' only execute in non-coroutine environment.');
        }

        SwooleRuntime::enableCoroutine(true, $flags);

        $result = SwooleCoroutine\run(...(array) $callbacks);

        SwooleRuntime::enableCoroutine(false);

        return $result;
    }
}

if (! function_exists('go'))
{
    /**
     * 创建协程
     * @return bool|int
     */
    function go(callable $callable)
    {
        $id = Coroutine::create($callable);
        return $id > 0 ? $id : false;
    }
}

if (! function_exists('swoole_enable_coroutine'))
{
    /**
     * 开启/设置 Swoole 协程 Hook
     * @param $flags
     */
    function swoole_enable_coroutine($flags = SWOOLE_HOOK_ALL)
    {
        SwooleRuntime::enableCoroutine(true, $flags);
    }
}
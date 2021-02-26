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

use Exception;
use Throwable;
use BadMethodCallException;
use Swoole\Coroutine as SwooleCoroutine;

/**
 * Class Coroutine
 * @method static SwooleCoroutine defer($callback)
 * @method static SwooleCoroutine set($options)
 * @method static SwooleCoroutine exists($cid)
 * @method static SwooleCoroutine yield()
 * @method static SwooleCoroutine suspend()
 * @method static SwooleCoroutine resume($cid)
 * @method static SwooleCoroutine stats()
 * @method static SwooleCoroutine getuid()
 * @method static SwooleCoroutine getCid()
 * @method static SwooleCoroutine getPcid($cid = null)
 * @method static SwooleCoroutine getContext($cid = null)
 * @method static SwooleCoroutine getBackTrace($cid = null, $options = null, $limit = null)
 * @method static SwooleCoroutine getElapsed($cid = null)
 * @method static SwooleCoroutine list()
 * @method static SwooleCoroutine listCoroutines()
 * @method static SwooleCoroutine enableScheduler()
 * @method static SwooleCoroutine disableScheduler()
 * @method static SwooleCoroutine gethostbyname($domain_name, $family = null, $timeout = null)
 * @method static SwooleCoroutine dnsLookup($domain_name, $timeout = null)
 * @method static SwooleCoroutine exec($command, $get_error_stream = null)
 * @method static SwooleCoroutine sleep($seconds)
 * @method static SwooleCoroutine getaddrinfo($hostname, $family = null, $socktype = null, $protocol = null, $service = null, $timeout = null)
 * @method static SwooleCoroutine statvfs($path)
 * @method static SwooleCoroutine readFile($filename)
 * @method static SwooleCoroutine writeFile($filename, $data, $flags = null)
 * @method static SwooleCoroutine wait($timeout = null)
 * @method static SwooleCoroutine waitPid($pid, $timeout = null)
 * @method static SwooleCoroutine waitSignal($signo, $timeout = null)
 * @method static SwooleCoroutine waitEvent($fd, $events = null, $timeout = null)
 * @method static SwooleCoroutine fread($handle, $length = null)
 * @method static SwooleCoroutine fgets($handle)
 * @method static SwooleCoroutine fwrite($handle, $string, $length = null)
 * @package Raylin666\Utils\Coroutine
 */
class Coroutine
{
    /**
     * @param $name
     * @param $arguments
     * @return BadMethodCallException|SwooleCoroutine
     */
    public static function __callStatic($name, $arguments)
    {
        if (! method_exists(SwooleCoroutine::class, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s.', $name));
        }

        return SwooleCoroutine::$name(...$arguments);
    }

    /**
     * Returns the parent coroutine ID.
     * Returns -1 when running in the top level coroutine.
     * Returns null when running in non-coroutine context.
     *
     * @see https://github.com/swoole/swoole-src/pull/2669/files#diff-3bdf726b0ac53be7e274b60d59e6ec80R940
     */
    public static function parentId(?int $coroutineId = null): ?int
    {
        $cid = SwooleCoroutine::getPcid($coroutineId);

        if ($cid === false) {
            return null;
        }

        return $cid;
    }

    /**
     * @return int Returns the coroutine ID of the coroutine just created.
     *             Returns -1 when coroutine create failed.
     */
    public static function create(callable $callable): int
    {
        $result = SwooleCoroutine::create(function () use ($callable) {
            try {
                call($callable);
            } catch (Throwable $throwable) {
                throw new Exception($throwable);
            }
        });

        return is_int($result) ? $result : -1;
    }

    /**
     * Returns the current coroutine ID.
     * Returns -1 when running in non-coroutine context.
     */
    public static function id(): int
    {
        return SwooleCoroutine::getCid();
    }

    /**
     * 是否在协程
     * @return bool
     */
    public static function inCoroutine(): bool
    {
        return Coroutine::id() > 0;
    }
}

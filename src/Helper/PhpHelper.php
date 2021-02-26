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

use Throwable;

/**
 * Class PhpHelper
 * @package Raylin666\Utils\Helper
 */
class PhpHelper
{
    /**
     * Call by callback
     *
     * @param callable|array|string $callback   callback
     * @param array          $args arguments
     *
     * @return mixed
     */
    public static function call($callback, ...$args)
    {
        if (is_string($callback)) {
            // className::method
            if (strpos($callback, '::') > 0) {
                $callback = explode('::', $callback, 2);
                // function
            } elseif (function_exists($callback)) {
                return $callback(...$args);
            }
        } elseif (is_object($callback) && method_exists($callback, '__invoke')) {
            return $callback(...$args);
        }

        if (is_array($callback)) {
            [$obj, $mhd] = $callback;
            return is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $callback(...$args);
    }

    /**
     * Call by callback
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return mixed
     */
    public static function callByArray($callback, array $args = [])
    {
        return self::call($callback, ...$args);
    }

    /**
     * dump vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function dumpVars(...$args): string
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return preg_replace("/=>\n\s+/", '=> ', $string);
    }

    /**
     * print vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function printVars(...$args): string
    {
        $string = '';

        foreach ($args as $arg) {
            $string .= print_r($arg, 1) . PHP_EOL;
        }

        return preg_replace("/Array\n\s+\(/", 'Array (', $string);
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    public static function exportVar($var): string
    {
        $string = var_export($var, true);

        return preg_replace('/=>\s+\n\s+array \(/', '=> array (', $string);
    }

    /**
     * @param Throwable $e
     * @param string    $title
     * @param bool      $debug
     *
     * @return string
     */
    public static function exceptionToString(Throwable $e, string $title = '', bool $debug = false): string
    {
        $errClass = get_class($e);

        if (false === $debug) {
            return sprintf('%s %s(code:%d) %s', $title, $errClass, $e->getCode(), $e->getMessage());
        }

        return sprintf('%s%s(code:%d): %s At %s line %d',
            $title ? $title . ' - ' : '',
            $errClass,
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
    }

    /**
     * @param Throwable $e
     * @param bool      $debug
     *
     * @return array
     */
    public static function exceptionToArray(Throwable $e, bool $debug = false): array
    {
        if (false === $debug) {
            return [
                'code'  => $e->getCode(),
                'error' => $e->getMessage(),
            ];
        }

        return [
            'code'  => $e->getCode(),
            'error' => sprintf('(%s) %s', get_class($e), $e->getMessage()),
            'file'  => sprintf('At %s line %d', $e->getFile(), $e->getLine()),
            'trace' => $e->getTraceAsString(),
        ];
    }

    /**
     * opcache clear
     */
    public static function opCacheClear()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * is Cli env
     *
     * @return  boolean
     */
    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * is phpdbg env
     *
     * @return  boolean
     */
    public static function isPhpDbg(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }

    /**
     * is windows OS
     *
     * @return bool
     */
    public static function isWindows(): bool
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }

    /**
     * is mac os
     *
     * @return bool
     */
    public static function isMac(): bool
    {
        return stripos(PHP_OS, 'Darwin') !== false;
    }
}

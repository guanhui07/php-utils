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

use RuntimeException;
use Composer\Autoload\ClassLoader;
use function file_get_contents;
use function is_array;
use function json_decode;

/**
 * Class ComposerHelper
 * @package Raylin666\Utils\Helper
 */
class ComposerHelper
{
    /**
     * @var null|CollectionHelper
     */
    private static $content;

    /**
     * @var null|CollectionHelper
     */
    private static $json;

    /**
     * @var array
     */
    private static $extra = [];

    /**
     * @var array
     */
    private static $scripts = [];

    /**
     * @var array
     */
    private static $versions = [];

    /**
     * @var null|ClassLoader
     */
    private static $classLoader;

    /**
     * @param $composer_lock_path   composer.lock Path      [__DIR__ . '/composer.lock']
     * @throws RuntimeException When composer.lock does not exist.
     */
    public static function getLockContent($composer_lock_path): CollectionHelper
    {
        if (! self::$content) {
            $path = self::discoverLockFile($composer_lock_path);
            if (! $path) {
                throw new RuntimeException('composer.lock not found.');
            }
            self::$content = new CollectionHelper(json_decode(file_get_contents($path), true));
            $packages = self::$content->offsetGet('packages') ?? [];
            $packagesDev = self::$content->offsetGet('packages-dev') ?? [];
            foreach (array_merge($packages, $packagesDev) as $package) {
                $packageName = '';
                foreach ($package ?? [] as $key => $value) {
                    if ($key === 'name') {
                        $packageName = $value;
                        continue;
                    }
                    switch ($key) {
                        case 'extra':
                            $packageName && self::$extra[$packageName] = $value;
                            break;
                        case 'scripts':
                            $packageName && self::$scripts[$packageName] = $value;
                            break;
                        case 'version':
                            $packageName && self::$versions[$packageName] = $value;
                            break;
                    }
                }
            }
        }
        return self::$content;
    }

    /**
     * @param $composer_json_path   composer.json Path     [__DIR__ . '/composer.json']
     * @return CollectionHelper
     */
    public static function getJsonContent($composer_json_path): CollectionHelper
    {
        if (! self::$json) {
            if (! is_readable($composer_json_path)) {
                throw new RuntimeException('composer.json is not readable.');
            }
            self::$json = new CollectionHelper(json_decode(file_get_contents($composer_json_path), true));
        }
        return self::$json;
    }

    /**
     * @param $composer_lock_path   composer.lock Path      [__DIR__ . '/composer.lock']
     * @return string
     */
    public static function discoverLockFile($composer_lock_path): string
    {
        if (is_readable($composer_lock_path)) {
            return $composer_lock_path;
        }
    }

    /**
     * @param string|null $key
     * @return array
     */
    public static function getMergedExtra(string $key = null)
    {
        if (! self::$extra) {
            self::getLockContent();
        }
        if ($key === null) {
            return self::$extra;
        }
        $extra = [];
        foreach (self::$extra ?? [] as $project => $config) {
            foreach ($config ?? [] as $configKey => $item) {
                if ($key === $configKey && $item) {
                    foreach ($item ?? [] as $k => $v) {
                        if (is_array($v)) {
                            $extra[$k] = array_merge($extra[$k] ?? [], $v);
                        } else {
                            $extra[$k][] = $v;
                        }
                    }
                }
            }
        }
        return $extra;
    }

    /**
     * @return ClassLoader
     */
    public static function getLoader(): ClassLoader
    {
        if (! self::$classLoader) {
            self::$classLoader = self::findLoader();
        }
        return self::$classLoader;
    }

    /**
     * @param ClassLoader $classLoader
     * @return ClassLoader
     */
    public static function setLoader(ClassLoader $classLoader): ClassLoader
    {
        self::$classLoader = $classLoader;
        return $classLoader;
    }

    /**
     * @return ClassLoader
     */
    private static function findLoader(): ClassLoader
    {
        $composerClass = '';
        foreach (get_declared_classes() as $declaredClass) {
            if (strpos($declaredClass, 'ComposerAutoloaderInit') === 0 && method_exists($declaredClass, 'getLoader')) {
                $composerClass = $declaredClass;
                break;
            }
        }
        if (! $composerClass) {
            throw new RuntimeException('Composer loader not found.');
        }
        return $composerClass::getLoader();
    }
}

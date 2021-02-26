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

/*
|--------------------------------------------------------------------------
| Bean抽象数据构建 [仅获取protected 和public成员变量]
|--------------------------------------------------------------------------

class User extends BeanSpl
{
    protected $name;
    protected $password;
    protected $url;

    protected function setKeyMapping(): array
    {
        return [
            'name' => 'username'
        ];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }
}

$user = new User([
    'name' => 'linshan',
    'email' => '33@qq.cn',
    'new' => new ArrSpl()
], true);

var_dump($user->getProperty('email')); // 返回 33@qq.cn
var_dump((string) $user); // 返回 {"name":"linshan","password":null,"url":null,"email":"33@qq.cn"}
var_dump($user->toArray());
var_dump($user->getProperty('new'));

 */

namespace Raylin666\Utils\Spl;

use JsonSerializable;
use Raylin666\Utils\Helper\ReflectionHelper;
use ReflectionProperty;

/**
 * Class BeanSpl
 * @package Raylin666\Utils\Spl
 */
class BeanSpl implements JsonSerializable
{
    // 过滤不为null
    const FILTER_NOT_NULL = 1;

    // 过滤不为空
    const FILTER_NOT_EMPTY = 2; // 0 不算empty

    // 过滤为null
    const FILTER_NULL = 3;

    // 过滤为空
    const FILTER_EMPTY = 4;

    /**
     * 防止直接访问错误属性报错
     * @param $name
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    /**
     * 转换为json字符串
     * @return false|string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.

        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /**
     * BeanSpl constructor.
     * @param array $data
     * @param bool  $autoCreateProperty
     */
    public function __construct(array $data = [], $autoCreateProperty = false)
    {
        if ($data) {
            $this->arrayToBean($data, $autoCreateProperty);
        }
    }

    /**
     * 获取所有属性
     *
     * @return array
     * @throws \ReflectionException
     */
    final public function allProperty() : array
    {
        $data = [];

        $class = ReflectionHelper::doClass($this);
        $protectedAndPublic = $class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($protectedAndPublic as $item) {
            if ($item->isStatic()) {
                continue;
            }

            array_push($data, $item->getName());
        }

        return $data;
    }

    /**
     * 添加属性
     *
     * @param      $name
     * @param null $value
     */
    final public function addProperty($name, $value = null): void
    {
        $this->$name = $value;
    }

    /**
     * 获取属性
     *
     * @param $name
     * @return void|null
     */
    final public function getProperty($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * @return array|mixed
     */
    final public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.

        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * 转换为数组
     *
     * @param array|null $columns
     * @param null       $filter
     * @return array
     */
    public function toArray(array $columns = null, $filter = null) : array
    {
        $data = $this->jsonSerialize();

        if ($columns) {
            $data = array_intersect_key($data, array_flip($columns));
        }

        if ($filter === self::FILTER_NOT_NULL) {
            return array_filter($data, function ($val) {
                return !is_null($val);
            });
        }

        if ($filter === self::FILTER_NOT_EMPTY) {
            return array_filter($data, function ($val) {
                if ($val === 0 || $val === '0') {
                    return true;
                }
                return !empty($val);
            });
        }

        if ($filter === self::FILTER_NULL) {
            return array_filter($data, function ($val) {
                return is_null($val);
            });
        }

        if ($filter === self::FILTER_EMPTY) {
            return array_filter($data, function ($val) {
                return empty($val);
            });
        }

        if (is_callable($filter)) {
            return array_filter($data, $filter);
        }

        return $data;
    }

    /**
     * array 转为 bean 属性
     * @param array $data
     * @param bool  $autoCreateProperty
     * @return BeanSpl
     * @throws \ReflectionException
     */
    final private function arrayToBean(array $data = [], $autoCreateProperty = false): BeanSpl
    {
        $data = $this->dataKeyMap($data);

        if ($autoCreateProperty == false) {
            $data = array_intersect_key($data, array_flip($this->allProperty()));
        }

        foreach ($data as $key => $item) {
            $this->addProperty($key, $item);
        }

        return $this;
    }

    /**
     * 如果需要用到keyMap(映射) 请在子类重构并返回对应的map映射数据
     * return ['dataKey'=>'beanKey']  dataKey为原key, beanKey为映射后的key
     * @return array
     */
    protected function setKeyMapping(): array
    {
        return [];
    }

    /**
     * dataKeyMap 将data中的键名 转化为Bean的属性名
     * @param array $array
     * @return array
     */
    final private function dataKeyMap(array $array): array
    {
        foreach ($this->setKeyMapping() as $dataKey => $beanKey) {
            if (array_key_exists($dataKey, $array)) {
                $array[$beanKey] = $array[$dataKey];
                unset($array[$dataKey]);
            }
        }

        return $array;
    }
}
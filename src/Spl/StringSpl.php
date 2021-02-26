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
| 字符串数据构建
|--------------------------------------------------------------------------

$string = new StringSpl('hello world!');
var_dump((array) $string->split(4));

// 输出：
array:3 [▼
    0 => "hell"
    1 => "o wo"
    2 => "rld!"
]

$string->setString('大家好, 才是真的好呀！');
var_dump((array) $string->explode());

// 输出：
array:2 [▼
    0 => "大家好"
    1 => " 才是真的好呀！"
]

$string->setString('Hello World!dsfsf reri fb_f-f');
// var_dump((string)$string->between('H', 'r'));   // ello Wo
var_dump($string->exists('H', false));      //  true
// var_dump((string)$string->snake('&'));   //  hello&world!dsfsf&reri&fb_f-f"
var_dump((string) $string);                 //  hello&world!dsfsf&reri&fb_f-f
var_dump((string) $string->studly());       //  Hello&world!dsfsf&reri&fbFF
var_dump((string) $string->camel());        //  helloWorld!dsfsfReriFbFF
var_dump((string) $string->replaceArray('f', ['M', 'M ']));     // helloWorld!dsMsM ReriFbFF
var_dump((string) $string->replaceLast('F', 'HHH'));     // helloWorld!dsMsM ReriFbFHHH
// var_dump((string) $string->start('apiserver:'));     // apiserver:helloWorld!dsMsM ReriFbFHHH
// var_dump((string) $string->before('M'));     // helloWorld!ds
// var_dump((string) $string->after('M'));     //  sM ReriFbFHHH
var_dump($string->startsWith('h'));         // true
var_dump($string->endsWith('HHH'));         // true

 */

namespace Raylin666\Utils\Spl;

/**
 * Class StringSpl
 * @package Raylin666\Utils\Spl
 */
class StringSpl extends StreamSpl
{
    /**
     * @var array
     */
    const MB_DETECT_ENCODING_LIST = [
        'UTF-8',
        'ASCII',
        'GBK',
        'GB2312',
        'LATIN1',
        'BIG5',
        'UCS-2',
    ];

    /**
     * Str constructor.
     * @param string $string
     */
    public function __construct(string $string = '')
    {
        parent::__construct($string);
    }

    /**
     * 设置字符串
     *
     * @param string $string
     * @return StringSpl
     */
    public function setString(string $string): StringSpl
    {
        $this->truncate();
        $this->rewind();
        $this->write($string);
        return $this;
    }

    /**
     * 把字符串分割到数组中
     *      $string = new StringSpl('hello world!');
     *      dump((array)$string->split(4));
     *      返回：^ array:3 [▼
                        0 => "hell"
                        1 => "o wo"
                        2 => "rld!"
                    ]
     *
     * @param int $length
     * @return ArraySpl
     */
    public function split(int $length = 1): ArraySpl
    {
        return new ArraySpl(str_split($this->__toString(), $length));
    }

    /**
     * 把字符串打散为数组
     *
     * @param string $delimiter
     * @return ArraySpl
     */
    public function explode(string $delimiter = ','): ArraySpl
    {
        return new ArraySpl(explode($delimiter, $this->__toString()));
    }

    /**
     * 字符串截取
     *
     * @param int $length
     * @param int $start
     * @return StringSpl
     */
    public function substr(int $length, int $start = 0): StringSpl
    {
        return $this->setString(substr($this->__toString(), $start, $length));
    }

    /**
     * @param string $str
     * @param bool   $ignoreCase    是否忽略大小写比较字符串, 默认不忽略
     * @return int   如果 str1 小于 str2 返回 < 0； 如果 str1 大于 str2 返回 > 0；如果两者相等，返回 0。
     */
    public function compare(string $string, bool $ignoreCase = false) : int
    {
        // strcmp and strcasecmp 二进制安全字符串比较
        return $ignoreCase ? strcasecmp($this->__toString(), $string) : strcmp($this->__toString(), $string);
    }

    /**
     * 编码识别转换
     *
     * @param string $desEncoding
     * @param array  $detectList
     * @return Str
     */
    public function encodingConvert(string $desEncoding, $detectList = self::MB_DETECT_ENCODING_LIST): Str
    {
        $fileType = mb_detect_encoding($this->__toString(), $detectList);
        if ($fileType != $desEncoding) {
            $this->setString(mb_convert_encoding($this->__toString(), $desEncoding, $fileType));
        }

        return $this;
    }

    /**
     * unicode 转换为 utf8 编码
     *
     * @return StringSpl
     */
    public function unicodeToUtf8(): StringSpl
    {
        // preg_replace_callback 执行一个正则表达式搜索并且使用一个回调进行替换
        $string = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function ($matches) {
            return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE');
        }, $this->__toString());
        return $this->setString($string);
    }

    /**
     * 转换为 unicode 编码
     *
     * @return StringSpl
     */
    public function toUnicode(): StringSpl
    {
        $raw = (string) $this->encodingConvert('UCS-2');
        $len = strlen($raw);

        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $raw[$i];
            $c2 = $raw[$i + 1];

            if (ord($c) > 0) {   // 两个字节的文字
                $str .= '\u' . base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u' . str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }

        $string = strtoupper($str); // 转换为大写

        return $this->setString($string);
    }

    /**
     * 去除字符串首尾处的空白字符（或者其他字符）
     *
     * @param string $charList      规定从字符串中删除哪些字符
     * @return StringSpl
     */
    public function trim(string $charList = " \t\n\r\0\x0B"): StringSpl
    {
        return $this->setString(trim($this->__toString(), $charList));
    }

    /**
     * 删除字符串开头的空白字符(或其他字符)
     *
     * @param string $charList      规定从字符串中删除哪些字符
     * @return StringSpl
     */
    public function ltrim(string $charList = " \t\n\r\0\x0B"): StringSpl
    {
        return $this->setString(ltrim($this->__toString(), $charList));
    }

    /**
     * 删除字符串末端的空白字符（或者其他字符）
     *
     * @param string $charList      规定从字符串中删除哪些字符
     * @return StringSpl
     */
    public function rtrim(string $charList = " \t\n\r\0\x0B"): StringSpl
    {
        return $this->setString(rtrim($this->__toString(), $charList));
    }

    /**
     * 把字符串填充为新的长度
     *
     * @param int         $length       规定新字符串的长度。如果该值小于原始字符串的长度，则不进行任何操作
     * @param string      $padString    规定供填充使用的字符串。默认是空字符串
     * @param int         $pad_type     规定填充字符串的哪边
     *      STR_PAD_BOTH - 填充字符串的两侧。如果不是偶数，则右侧获得额外的填充。
            STR_PAD_LEFT - 填充字符串的左侧。
            STR_PAD_RIGHT - 填充字符串的右侧。这是默认的
     *
     * @return StringSpl
     */
    public function pad(int $length, string $padString = ' ', int $pad_type = STR_PAD_RIGHT): StringSpl
    {
        return $this->setString(str_pad($this->__toString(), $length, $padString, $pad_type));
    }

    /**
     * 重复使用指定字符串
     *
     * @param int $count            重复次数
     * @return StringSpl
     */
    public function repeat(int $count): StringSpl
    {
        return $this->setString(str_repeat($this->__toString(), $count));
    }

    /**
     * 获取字符串长度
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->__toString());
    }

    /**
     * 将字符串转化为全大写
     *
     * @return StringSpl
     */
    public function upper(): StringSpl
    {
        return $this->setString(strtoupper($this->__toString()));
    }

    /**
     * 将字符串转化为全小写
     *
     * @return StringSpl
     */
    public function lower(): StringSpl
    {
        return $this->setString(strtolower($this->__toString()));
    }

    /**
     * 剥去字符串中的 HTML、XML 以及 PHP 的标签
     *
     * @param string|null $allowable_tags
     * @return StringSpl
     */
    public function stripTags(string $allowable_tags = null): StringSpl
    {
        return $this->setString(strip_tags($this->__toString(), $allowable_tags));
    }

    /**
     * 字符串替换
     *
     * @param string $find
     * @param string $replaceTo
     * @return StringSpl
     */
    public function replace(string $find, string $replaceTo): StringSpl
    {
        return $this->setString(str_replace($find, $replaceTo, $this->__toString()));
    }

    /**
     * 获取区间字符串
     *
     * @param string $startStr
     * @param string $endStr
     * @return StringSpl
     */
    public function between(string $startStr, string $endStr): StringSpl
    {
        $explode_arr = explode($startStr, $this->__toString());

        if (isset($explode_arr[1])) {
            $explode_arr = explode($endStr, $explode_arr[1]);
            return $this->setString($explode_arr[0]);
        }

        return $this->setString('');
    }

    /**
     * 正则表达式匹配
     *
     * @param      $regex               正则表达式
     * @param bool $rawReturn
     * @return mixed|null
     */
    public function regex($regex, bool $rawReturn = false)
    {
        preg_match($regex, $this->__toString(), $result);
        return (! empty($result)) ? ($rawReturn ? $result : $result[0]) : null;
    }

    /**
     * 字符串是否存在
     *
     * @param string $find                  需要查找的字符串
     * @param bool   $ignoreCase            是否忽略大小写, 默认忽略
     * @return bool
     */
    public function exists(string $find, bool $ignoreCase = true): bool
    {
        return (($ignoreCase ? stripos($this->__toString(), $find) : strpos($this->__toString(), $find)) === false) ?
            false : true;
    }

    /**
     * 替换为 $delimiter 的样子
     *
     * @param string $delimiter
     * @return StringSpl
     */
    public function snake(string $delimiter = '_'): StringSpl
    {
        $string = $this->__toString();

        if (! ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', ucwords($this->__toString()));
            $string = $this->setString(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $string));
            $this->setString($string);
            $this->lower();
        }

        return $this;
    }

    /**
     * 将字符串转为大写的大写
     *
     * @return StringSpl
     */
    public function studly(): StringSpl
    {
        return $this->setString(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $this->__toString()))));
    }

    /**
     * 将字符串转为驼峰
     *
     * @return StringSpl
     */
    public function camel(): StringSpl
    {
        $this->studly();
        return $this->setString(lcfirst($this->__toString()));
    }

    /**
     * 用数组逐个字符替换  [区分大小写]
     *
     * @param  string $search
     * @param  array  $replace
     * @return StringSpl
     */
    public function replaceArray(string $search, array $replace): StringSpl
    {
        foreach ($replace as $value) {
            $this->setString($this->replaceFirst($search, $value));
        }

        return $this;
    }

    /**
     * 替换字符串中给定值的第一次出现  [区分大小写]
     *
     * @param string $search
     * @param string $replace
     * @return StringSpl
     */
    public function replaceFirst(string $search, string $replace): StringSpl
    {
        if ($search == '') {
            return $this;
        }

        $position = strpos($this->__toString(), $search);

        if ($position !== false) {
            return $this->setString(substr_replace($this->__toString(), $replace, $position, strlen($search)));
        }

        return $this;
    }

    /**
     * 替换字符串中给定值的最后一次出现  [区分大小写]
     *
     * @param string $search
     * @param string $replace
     * @return StringSpl
     */
    public function replaceLast(string $search, string $replace): StringSpl
    {
        $position = strrpos($this->__toString(), $search);

        if ($position !== false) {
            return $this->setString(substr_replace($this->__toString(), $replace, $position, strlen($search)));
        }

        return $this;
    }

    /**
     * 以一个给定值的单一实例开始一个字符串 [字符串前缀]
     *
     * @param string $prefix
     * @return StringSpl
     */
    public function start(string $prefix): StringSpl
    {
        return $this->setString($prefix . preg_replace('/^(?:' . preg_quote($prefix, '/') . ')+/u', '', $this->__toString()));
    }

    /**
     * 在给定的值之前获取字符串的一部分
     *
     * @param string $search
     * @return StringSpl
     */
    public function before(string $search): StringSpl
    {
        return ($search === '') ? $this : $this->setString(explode($search, $this->__toString())[0]);
    }

    /**
     * 在给定的值之后返回字符串的其余部分
     *
     * @param string $search
     * @return StringSpl
     */
    public function after(string $search): StringSpl
    {
        return ($search === '') ? $this : $this->setString(array_reverse(explode($search, $this->__toString(), 2))[0]);
    }

    /**
     * 确定给定的字符串是否从给定的子字符串开始
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public function startsWith($needles) : bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($this->__toString(), 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * 确定给定的字符串是否以给定的子字符串结束
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public function endsWith($needles) : bool
    {
        foreach ((array)$needles as $needle) {
            if (substr($this->__toString(), -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}
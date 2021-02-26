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
| Spl流数据构建
|--------------------------------------------------------------------------

class User
{
    public function __toString()
    {
        return json_encode($this);
    }
}

$stream = new StreamSpl(fopen(__DIR__ . '/composer.json', 'r+'));
// $stream = new StreamSpl(new User());
//$stream->seek(30);
//var_dump($stream->read(4));
var_dump($stream->write('hello world!'));
var_dump($stream->truncate(4));
$stream->rewind();
var_dump($stream->getContents());

 */

namespace Raylin666\Utils\Spl;

use RuntimeException;
use Exception;
use InvalidArgumentException;

/**
 * Class StreamSpl
 * @package Raylin666\Utils\Spl
 */
class StreamSpl
{
    /**
     * 流资源
     * @var bool|resource
     */
    private $stream;

    /**
     * 可查询
     * @var
     */
    private $seekable;

    /**
     * 可读
     * @var bool
     */
    private $readable;

    /**
     * 可写
     * @var bool
     */
    private $writable;

    /**
     * 可读权限
     * @var array
     */
    private $readList = [
        'r' => true,
        'w+' => true,
        'r+' => true,
        'x+' => true,
        'c+' => true,
        'rb' => true,
        'w+b' => true,
        'r+b' => true,
        'x+b' => true,
        'c+b' => true,
        'rt' => true,
        'w+t' => true,
        'r+t' => true,
        'x+t' => true,
        'c+t' => true,
        'a+' => true
    ];

    /**
     * 可写权限
     * @var array
     */
    private $writeList = [
        'w' => true,
        'w+' => true,
        'rw' => true,
        'r+' => true,
        'x+' => true,
        'c+' => true,
        'wb' => true,
        'w+b' => true,
        'r+b' => true,
        'x+b' => true,
        'c+b' => true,
        'w+t' => true,
        'r+t' => true,
        'x+t' => true,
        'c+t' => true,
        'a' => true,
        'a+' => true
    ];

    /**
     * StreamSpl constructor.
     * @param string $resource
     * @param string $mode
     */
    public function __construct($resource = '', $mode = 'r+')
    {
        $resource_type = gettype($resource);

        switch ($resource_type) {
            case 'resource':        // 资源类型
                {
                    $this->stream = $resource;
                    break;
                }
            case 'object':          // 资源类型
                {
                    if (method_exists($resource, '__toString')) {
                        $resource = $resource->__toString();
                        $this->stream = fopen('php://memory', $mode);
                        if ($resource !== '') {
                            fwrite($this->stream, $resource);
                        }
                    } else {
                        throw new InvalidArgumentException('Invalid resource type: ' . $resource_type);
                    }
                    break;
                }
            default:
                {
                    $this->stream = fopen('php://memory', $mode);
                    try {
                        $resource = (string) $resource;
                        if ($resource !== '') {
                            fwrite($this->stream, $resource);
                        }
                    } catch (Exception $exception) {
                        throw new InvalidArgumentException('Invalid resource type: ' . $resource_type);
                    }
                }
        }

        $info = stream_get_meta_data($this->stream);
        $this->seekable = $info['seekable'];
        $this->readable = isset($this->readList[$info['mode']]);
        $this->writable = isset($this->writeList[$info['mode']]);
    }

    /**
     * 流数据写入 (将直接写入到流文件)
     *
     * @param $string
     * @return bool|int
     */
    public function write($string)
    {
        if (! $this->writable) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }

        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * 流数据读取
     *
     * @param $length
     * @return bool|string
     */
    public function read($length)
    {
        if (! $this->readable) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }

        if ($length < 0) {
            throw new RuntimeException('Length parameter cannot be negative');
        }

        if (0 === $length) {
            return '';
        }

        $string = fread($this->stream, $length);
        if (false === $string) {
            throw new RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    /**
     * 转字符串 (string) StreamSpl
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.

        try {
            $this->rewind();
            return (string) stream_get_contents($this->stream);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * 关闭流
     */
    public function close()
    {
        $res = $this->detach();
        if (is_resource($res)) fclose($res);
    }

    /**
     * 清理流信息 (恢复重置数据)
     * @return bool|resource|null
     */
    public function detach()
    {
        if (! isset($this->stream)) {
            return null;
        }

        $this->readable = $this->writable = $this->seekable = false;
        $result = $this->stream;
        unset($this->stream);
        return $result;
    }

    /**
     * 获取流大小(字节|单位:B)
     * @return |null
     */
    public function getSize()
    {
        $stats = fstat($this->stream);
        return isset($stats['size']) ? $stats['size'] : null;
    }

    /**
     * 返回文件指针的当前位置
     *
     * @return bool|int
     */
    public function tell()
    {
        $result = ftell($this->stream);

        if ($result === false) {
            throw new RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    /**
     * 设置文件指针stream的位置
     *
     * @param     $offset
     * @param int $whence
     * @return $this
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (! $this->seekable) {
            throw new RuntimeException('Stream is not seekable');
        } else if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Unable to seek to stream position '
                . $offset . ' with whence ' . var_export($whence, true));
        }

        return $this;
    }

    /**
     * 检查是否已到达文件末尾 [对遍历长度未知的数据很有用]
     *
     * @return bool
     */
    public function eof()
    {
        return !$this->stream || feof($this->stream);
    }

    /**
     * 重置文件指针stream的位置
     *
     * @return $this
     */
    public function rewind()
    {
        $this->seek(0);
        return $this;
    }

    /**
     * 是否可寻
     *
     * @return mixed
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * 是否可写
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * 是否可读
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * 获取流内容
     *
     * @param bool $rewind 默认情况下 获取的内容从 $this->tell() 的位置开始, 如果设置 true 则会重置文件指针stream的位置
     * @return bool|string
     */
    public function getContents($rewind = false)
    {
        if ($rewind) {
            $this->rewind();            // 重置文件指针stream的位置
        }

        // 注意与__toString的区别
        $contents = stream_get_contents($this->stream);

        if ($contents === false) {
            throw new RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    /**
     * 获取流信息
     *
     * @param null $key
     * @return array|null
     */
    public function getMetaData($key = null)
    {
        if (! isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);

        if (! $key) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }

    /**
     * 获取流资源
     *
     * @return bool|resource
     */
    public function getStreamResource()
    {
        return $this->stream;
    }

    /**
     * 获取文件指针，处理并截断文件的长度和大小
     *
     * @param int $size
     * @return bool
     */
    public function truncate($size = 0)
    {
        return ftruncate($this->stream, $size);
    }

    public function __destruct()
    {
        $this->close();
    }
}
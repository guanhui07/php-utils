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
| 文件流构建
|--------------------------------------------------------------------------
 */

namespace Raylin666\Utils\Spl;

/**
 * Class FileStreamSpl
 * @package Raylin666\Utils\Spl
 */
class FileStreamSpl extends Stream
{
    /**
     * FileStreamSpl constructor.
     * @param        $file
     * @param string $mode
     */
    public function __construct($file, $mode = 'c+')
    {
        parent::__construct(fopen($file, $mode));
    }

    /**
     * 加锁
     *
     * @param int $mode
     * @return bool
     */
    public function lock($mode = LOCK_EX)
    {
        return flock($this->getStreamResource(), $mode);
    }

    /**
     * 解锁
     *
     * @param int $mode
     * @return bool
     */
    public function unlock($mode = LOCK_UN)
    {
        return flock($this->getStreamResource(), $mode);
    }
}
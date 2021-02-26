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

use DateTime;

/**
 * Class DateTimeHelper
 * @package Raylin666\Utils\Helper
 */
class DateTimeHelper extends DateTime
{
    /**
     * getDayStartend 返回一天的开始和结束时间(默认今天) - 时间戳格式
     * @param int $timestamp
     * @return array
     */
    public static function getDayStartend(int $timestamp = 0)
    {
        if (! $timestamp) {
            $day = date('Y-m-d');
        } else {
            $day = date('Y-m-d', $timestamp);
        }

        $day = explode('-', $day);
        list($y, $m, $d) = $day;

        list($startTime, $endTime) = [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y)
        ];

        return [$startTime, $endTime];
    }
}

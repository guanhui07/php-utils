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

/**
 * Class HttpHelper
 * @package Raylin666\Utils\Helper
 */
class HttpHelper
{
    /**
     * CURL请求接口数据
     *
     * @param  [type]  $curl        请求地址
     * @param  boolean $https       https ? http
     * @param  string  $method      提交方式
     * @param  [type]  $data        提交数据
     * @param  array   $headers     HEADER头设置
     * @param  array   $timeout     CURL超时时间
     */
    public static function curl(
        $curl,
        $https = false,
        $method = 'GET',
        $data = null,
        array $headers = [],
        $timeout = 30
    )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);     // 从证书中检查SSL加密算法是否存在
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $_request = curl_exec($ch);

        curl_close($ch);

        return $_request;
    }

    /**
     * 下载数据文件(将数据打包以文件形式下载)
     * @param       $stringData  需要下载的内容
     * @param null  $fileName    下载的文件名称
     * @param array $headers      Header头设置
     */
    public static function downloadDataFile($stringData, $fileName = null, $headers = [] )
    {
        header('Content-type:' . ArrayHelper::get($headers, 'Content-type', 'application/octet-stream'));
        header('Accept-Ranges:' . ArrayHelper::get($headers, 'Accept-Ranges', 'bytes'));
        header('Accept-Length:' . strlen($stringData));
        header('Content-Disposition:attachment;filename=' . $fileName);
        header('Content-Transfer-Encoding:' . ArrayHelper::get($headers, 'Content-Transfer-Encoding', 'binary'));
        header('Cache-Control:' . ArrayHelper::get($headers, 'Cache-Control', 'no-cache,no-store,max-age=0,must-revalidate'));
        header('Pragma:' . ArrayHelper::get($headers, 'Pragma', 'no-cache'));

        echo $stringData;
    }
}

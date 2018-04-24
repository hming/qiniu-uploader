<?php

/*
 * This file is part of the car/chedianai_bc.
 *
 * (c) chedianai <i@chedianai.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Library\Util;

use App\Image;

/**
 * Created by PhpStorm.
 * Author: Xiaoming <wanghaiming@juhaoke.com>
 * Date: 2017/11/21
 * Time: 15:13.
 */
class ImageUtil
{
    public static $uploader;
    public static $accessDomain;

    /**
     * 下载远程图片到本地.
     *
     * @param $url
     * @param $path
     * @param $fileName
     * @param string $fileExt
     *
     * @return string
     *
     * @throws \Exception
     *
     * @author zhuzhengqian@vchangyi.com
     */
    public static function downloadRemoteImage($url, $path, $fileName, $fileExt = 'png')
    {
        $desDir = $path;

        $desFile = $desDir.DIRECTORY_SEPARATOR.$fileName.'.'.$fileExt;
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            file_put_contents($desFile, $output);
        } catch (\Exception $e) {
            throw new \Exception('下载图片失败');
        }

        return '/upload/image/'.$fileName.'.png';
    }

    /**
     * 获得图片上传器实例.
     *
     * @return QiniuUploader
     *
     * @throws \Exception
     *
     * @author wanghaiming@vchangyi.com
     */
    public static function getUploaderInstance()
    {
        if (!self::$uploader) {
            self::$accessDomain = config('qiniu.access_domain');
            if (!self::$accessDomain) {
                throw new \Exception('No image uploader access domain.');
            }
            self::$uploader = new QiniuUploader(config('qiniu.access_key'), config('qiniu.secret_key'), config('qiniu.bucket'));
        }

        return self::$uploader;
    }

    /**
     * 上传图片.
     *
     * @param $path
     * @param $fileName
     * @param bool $needInfo 是否需要获得图片详情
     * @return Image|null
     * @throws \Exception
     */
    public static function upload($path, $fileName, $needInfo = false)
    {
        $uploader = self::getUploaderInstance();
        $ret      = $uploader->upload($path, $fileName);
        if ($ret && isset($ret['key'])) {
            $image            = new Image();
            $image->url       = 'https://'.self::$accessDomain.'/'.$ret['key'];
            $image->file_id   = $ret['key'];
            $image->size      = filesize($path);
            if ($needInfo) {
                $info = $uploader->imageInfo($image->url);
                if ($info) {
                    $image->width  = $info['width'];
                    $image->height = $info['height'];
                    $image->ext    = $info['format'];
                }
            }

            return $image;
        }

        return null;
    }

    /**
     * @param $url
     * @param $fileName
     * @param bool $needInfo
     *
     * @return Image|null
     *
     * @author wanghaiming@vchangyi.com
     */
    public static function remoteUpload($url, $fileName, $needInfo = false)
    {
        $uploader = self::getUploaderInstance();
        $ret      = $uploader->remoteUpload($url, $fileName);
        if ($ret && isset($ret['key'])) {
            $image            = new Image();
            $image->url       = 'http://'.self::$accessDomain.'/'.$ret['key'];
            $image->thumbnail = $image->url.'?imageView2/1/w/128/h/128';
            $image->size      = $ret['fsize'];
            $image->file_id   = $ret['key'];
            if ($needInfo) {
                $info = $uploader->imageInfo($image->url);
                if ($info) {
                    $image->width  = $info['width'];
                    $image->height = $info['height'];
                    $image->ext    = $info['format'];
                }
            }

            return $image;
        }

        return null;
    }
}

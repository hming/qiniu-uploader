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

use Qiniu\Auth;
use Qiniu\Http\Client;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

/**
 * 七牛上传文件包装类
 * Class QiniuUploader.
 */
class QiniuUploader
{
    private $accessKey;
    private $secretKey;
    private $bucket;

    /**
     * QiniuUploader constructor.
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param string $bucket
     *
     * @throws \Exception
     */
    public function __construct($accessKey, $secretKey, $bucket)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket    = $bucket;
    }

    /**
     * 上传文件.
     *
     * @author minco.wang@gmail.com
     *
     * @param $sourceFile <p>要上传文件的本地路径</p>
     * @param $fileName <p>上传到七牛后保存的文件名必须唯一</p>
     */
    public function upload($sourceFile, $fileName = null)
    {
        $this->checkConfig();
        if (!$fileName) {
            $fileName = time().sprintf('%04s', rand(0, 9999)).'.'.pathinfo($sourceFile, PATHINFO_EXTENSION);
        }
        $auth = new Auth($this->accessKey, $this->secretKey); //构建鉴权对象
        // 生成上传 Token
        $token = $auth->uploadToken($this->bucket);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr       = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $fileName, $sourceFile);
        if ($err) {
            \Log::error("[$err->code()]".$err->message());

            return null;
        }

        return $ret;
    }

    /**
     * 远程上传.
     *
     * @author minco.wang@gmail.com
     *
     * @param $sourceUrl <p>要上传文件的 url</p>
     * @param $fileName <p>上传到七牛后保存的文件名必须唯一</p>
     */
    public function remoteUpload($sourceUrl, $fileName = null)
    {
        $this->checkConfig();
        $auth            = new Auth($this->accessKey, $this->secretKey); //构建鉴权对象
        $bmgr            = new BucketManager($auth);
        list($ret, $err) = $bmgr->fetch($sourceUrl, $this->bucket, $fileName);
        if ($err) {
            \Log::error("[$err->code()]".$err->message());

            return null;
        }

        return $ret;
    }

    /**
     * 获取图片信息.
     *
     * @author minco.wang@gmail.com
     *
     * @param $qiniuUrl
     */
    public function imageInfo($qiniuUrl)
    {
        try {
            $rt = Client::get($qiniuUrl.'?imageInfo');

            return $rt->json();
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param string $accessKey
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * 检测必要的配置.
     *
     * @throws \Exception
     *
     * @author wanghaiming@vchangyi.com
     */
    public function checkConfig()
    {
        if (!$this->accessKey || !$this->secretKey) {
            throw new \Exception('No qiniu auth config.');
        }
        if (!$this->bucket) {
            throw new \Exception('No qiniu bucket config.');
        }
    }
}

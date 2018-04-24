<?php

/*
 * This file is part of the car/chedianai_bc.
 *
 * (c) chedianai <i@chedianai.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    'access_key'    => env('QINIU_ACCESS_KEY', 'rTo8b-qwy_J9-j9zbX5BPRzHJ29xDvF91pquBDsj'),
    'secret_key'    => env('QINIU_SECRET_KEY', 'BkoZazp5kQ2GZAoCniDyB7j-aWui47cb-XmshpJ8'),
    'bucket'        => env('QINIU_BUCKET', 'test-10001'),
    'access_domain' => env('QINIU_ACCESS_DOMAIN', 'test-10001.qiniudn.com'), //访问已上传的文件的域名
];

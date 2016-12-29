<?php

namespace tools;

require_once ROOT_PATH . '/library/org/qiniu/autoload.php';

// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class Qiniu
{
    private $spaceUrl = "http://o6qgg94pd.bkt.clouddn.com/";
    private $accessKey;
    private $secretKey;

    public function __construct()
    {
        $this->accessKey = 'ozdUXw6_VicYqQRXSoPO-R4th9dYoFNv7l25fvBO';
        $this->secretKey = 'C5ZbVJDI_x62-jeo6M_VINrcj5gFRA8T29-xifqP';
    }

    /**
     * demo
     * @param $filename
     * @param $uid
     * @return string
     */
    public function upload($filename, $uid)
    {
        // 构建鉴权对象
        $auth = new Auth($this->accessKey, $this->secretKey);

        // 要上传的空间
        $bucket = 'newhypo';

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 要上传文件的本地路径
        $filePath = $filename;

        $file = basename($filename, ".tbi");

        // 上传到七牛后保存的文件名
        $key = "wsy/$uid" . date("/Y/m/d/") . "$file.jpg";

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if (null !== $err) {
            var_dump($err);
        } else {
            return $this->spaceUrl . $key;
        }

    }
}

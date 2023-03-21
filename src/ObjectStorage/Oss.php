<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/24
 * Time: 1:39 下午
 */
declare(strict_types=1);

namespace PonyCool\ObjectStorage;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OSS\OssClient;
use OSS\Core\OssException;

class Oss implements ObjectStorageInterface
{
    private ObjectStorage $os;

    public function __construct(ObjectStorage $objectStorage)
    {
        $this->os = $objectStorage;
    }

    /**
     * 上传文件
     * @param string $filePath 文件原始路径包含文件名称
     * @param string $osPath OSS保存路径包含文件名称
     * @return string|null OSS文件地址
     * @throws Exception
     */
    public function upload(string $filePath, string $osPath): ?string
    {
        try {
            $ossClient = new OssClient($this->os->getAccessKey(), $this->os->getSecret(), $this->os->getRegion());
            $ossClient->uploadFile($this->os->getBucket(), $osPath, $filePath);
            if (is_null($this->os->getDomain())) {
                return str_replace('oss', $this->os->getBucket() . '.oss', $this->os->getRegion()) . '/' . $osPath;
            }
            return $this->os->getDomain() . '/' . $osPath;
        } catch (OssException $e) {
            $message = sprintf('OSS上传文件失败，%s', $e->getMessage());
            throw new Exception($message);
        }
    }

    /**
     * 下载文件
     * @param string $file OS文件名包含OS文件夹
     * @param string $savePath OS保存路径包含文件名称
     * @return bool
     * @throws Exception
     */
    public function download(string $file, string $savePath): bool
    {
        try {
            $options = [
                OssClient::OSS_FILE_DOWNLOAD => $savePath
            ];
            $ossClient = new OssClient($this->os->getAccessKey(), $this->os->getSecret(), $this->os->getRegion());
            $ossClient->getObject($this->os->getBucket(), $file, $options);
            return true;
        } catch (OssException $e) {
            $message = sprintf('OSS下载文件失败，%s', $e->getMessage());
            throw new Exception($message);
        }
    }

    /**
     * 获取直传签名
     * @return string
     * @throws Exception
     */
    public function getSignature(): string
    {
        if (is_null($this->os->getCallbackUrl())) {
            throw new Exception('未正确设置上传回调服务器的URL');
        }

        // AccessKeyId
        $id = $this->os->getAccessKey();
        // AccessKeySecret
        $key = $this->os->getSecret();
        $host = $this->os->getDomain();
        // 上传回调服务器的URL
        $callbackUrl = $this->os->getCallbackUrl();
        $callbackParams = [
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => 'application/x-www-form-urlencoded'
        ];
        $callbackStr = json_encode($callbackParams);

        $base64CallbackBody = base64_encode($callbackStr);
        $now = time();
        $expires = $this->os->getSignatureExpires();
        $end = $now + $expires;
        $expiration = self::gmtIso8601($end);

        // 最大文件大小
        $condition = [
            0 => 'content-length-range',
            1 => 0,
            2 => 1048576000
        ];
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $dir = $this->os->getPrefix() ?: '';
        $start = [
            0 => 'starts-with',
            1 => '$key',
            2 => $dir
        ];
        $conditions[] = $start;

        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions
        ];
        $policy = json_encode($arr);
        $base64Policy = base64_encode($policy);
        $strToSign = $base64Policy;
        $signature = base64_encode(hash_hmac('sha1', $strToSign, $key, true));

        $result = [
            'access_id' => $id,
            'host' => $host,
            'policy' => $base64Policy,
            'signature' => $signature,
            'expires' => $end,
            'callback' => $base64CallbackBody,
            'dir' => $dir
        ];
        return json_encode($result);
    }

    /**
     * @param int|null $time
     * @return string
     */
    private function gmtIso8601(?int $time): string
    {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }


    /**
     * 获取公钥
     * @param string $url 获取公钥的URL
     * @return string|bool
     */
    public function getPublicKey(string $url): string|bool
    {
        $client = new Client();
        try {
            $res = $client->request('GET', $url);
            return $res->getBody()->getContents();
        } catch (GuzzleException) {
            return false;
        }
    }

    /**
     * 直传回调验签
     * @return bool
     */
    public function verifyCallbackSignature(): bool
    {
        // 获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";

        // 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改
        // 配置文件以你的apache安装路径为准，在DirectoryIndex index.php这行下面增加以下两行
        // RewriteEngine On
        // RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])) {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {
            return false;
        }

        // 获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

        // 获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);

        $pubKey = $this->getPublicKey($pubKeyUrl);
        if ($pubKey === false) {
            return false;
        }
        if ($pubKey == "") {
            return false;
        }

        // 获取回调body
        $body = file_get_contents('php://input');

        // 拼接待签名字符串
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if ($pos === false) {
            $authStr = urldecode($path) . "\n" . $body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;
        }
        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1) {
            return true;
        } else {
            return false;
        }
    }
}

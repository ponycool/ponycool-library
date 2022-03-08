<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/11
 * Time: 2:35 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Utils;


class DecryptData
{
    private string $appId;
    private string $sessionKey;

    /**
     * 构造函数
     * @param $appId string 小程序的appId
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     */
    public function __construct(string $appId, string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appId = $appId;
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data array|null 解密后的原文
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData(string $encryptedData, string $iv, ?array &$data): int
    {
        if (strlen($this->sessionKey) != 24) {
            return ErrorCode::$IllegalAesKey;
        }
        $aesKey = base64_decode($this->sessionKey);


        if (strlen($iv) != 24) {
            return ErrorCode::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        if ($result === false) {
            return ErrorCode::$DecryptionFailed;
        }
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return ErrorCode::$IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->appId) {
            return ErrorCode::$IllegalBuffer;
        }
        $data = $result;
        return ErrorCode::$OK;
    }
}

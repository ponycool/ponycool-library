<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/26
 * Time: 9:08 上午
 */
declare(strict_types=1);

namespace PonyCool\Ai\FaceTransformation;

use PonyCool\Ai\Config\Conf;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\{ClientProfile, HttpProfile};
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ft\V20200304\FtClient;
use TencentCloud\Ft\V20200304\Models\ChangeAgePicRequest;

class Tencent implements FtInterface
{

    private Conf $config;

    /**
     * 配置检查
     * @param Conf $config
     * @return bool
     */
    public function check(Conf $config): bool
    {
        if (empty($config->getSecretId())) {
            return false;
        }
        if (empty($config->getSecretKey())) {
            return false;
        }
        if (is_null($config->getRegion())) {
            return false;
        }
        if (is_null($config->getImageBase64()) && is_null($config->getImageUrl())) {
            return false;
        }
        $this->config = $config;
        return true;
    }

    /**
     * 人脸年龄变化
     * @param int $age 变化到的人脸年龄 [10,80]
     * @return array
     */
    public function changeAge(int $age): array
    {
        try {
            $cred = new Credential($this->config->getSecretId(), $this->config->getSecretKey());
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("ft.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new FtClient($cred, $this->config->getRegion(), $clientProfile);

            $req = new ChangeAgePicRequest();

            $params = $this->getParams();
            $params['AgeInfos'] = [
                [
                    'Age' => $age
                ]
            ];
            $req->fromJsonString(json_encode($params));

            $resp = $client->ChangeAgePic($req);

            return [true, $resp->toJsonString()];
        } catch (TencentCloudSDKException $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * 获取参数
     * @return array
     */
    private function getParams(): array
    {
        $params = [
            'RspImgType' => 'url'
        ];
        if (!is_null($this->config->getImageBase64())) {
            $params = array_merge($params, ['Image' => $this->config->getImageBase64()]);
        }
        if (!is_null($this->config->getImageUrl())) {
            $params = array_merge($params, ['Url' => $this->config->getImageUrl()]);
        }
        return $params;
    }
}
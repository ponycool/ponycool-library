<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/26
 * Time: 9:08 上午
 */
declare(strict_types=1);

namespace PonyCool\Ai\ImageRecognition;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Tiia\V20190529\TiiaClient;
use TencentCloud\Tiia\V20190529\Models\RecognizeCarRequest;

class Tencent implements RecognizerInterface
{

    private Config $config;

    /**
     * @param Config $config
     * @return bool
     */
    public function checkConfig(Config $config): bool
    {
        if (empty($config->getSecretId())) {
            return false;
        }
        if (empty($config->getSecretKey())) {
            return false;
        }
        if (is_null($config->getEndpoint())) {
            return false;
        }
        if (is_null($config->getImageBase64()) && is_null($config->getImageUrl())) {
            return false;
        }
        $this->config = $config;
        return true;
    }

    /**
     * 汽车识别
     * @return array
     */
    public function recognizeCar(): array
    {
        try {

            $cred = new Credential($this->config->getSecretId(), $this->config->getSecretKey());
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->config->getEndpoint());

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new TiiaClient($cred, $this->config->getRegion(), $clientProfile);

            $req = new RecognizeCarRequest();

            $params = $this->getParams();
            $req->fromJsonString(json_encode($params));

            $resp = $client->RecognizeCar($req);

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
        $params = [];
        if (!is_null($this->config->getImageBase64())) {
            $params = array_merge($params, ['ImageBase64' => $this->config->getImageBase64()]);
        }
        if (!is_null($this->config->getImageUrl())) {
            $params = array_merge($params, ['ImageUrl' => $this->config->getImageUrl()]);
        }
        return $params;
    }
}
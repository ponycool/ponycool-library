<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/26
 * Time: 9:08 上午
 */
declare(strict_types=1);

namespace PonyCool\Ai\Ocr;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\{ClientProfile, HttpProfile};
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ocr\V20181119\OcrClient;
use TencentCloud\Ocr\V20181119\Models\{
    GeneralBasicOCRRequest,
    GeneralHandwritingOCRRequest,
    LicensePlateOCRRequest
};

class Tencent implements OcrInterface
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
     * 通用印刷识别
     * @return array
     */
    public function generalBasicOCR(): array
    {
        try {

            $cred = new Credential($this->config->getSecretId(), $this->config->getSecretKey());
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->config->getEndpoint());

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new OcrClient($cred, $this->config->getRegion(), $clientProfile);

            $req = new GeneralBasicOCRRequest();

            $params = $this->getParams();
            $req->fromJsonString(json_encode($params));

            $resp = $client->GeneralBasicOCR($req);

            return [true, $resp->toJsonString()];
        } catch (TencentCloudSDKException $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * 通用手写体识别
     * @return array
     */
    public function generalHandwritingOCR(): array
    {
        try {

            $cred = new Credential($this->config->getSecretId(), $this->config->getSecretKey());
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->config->getEndpoint());

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new OcrClient($cred, $this->config->getRegion(), $clientProfile);

            $req = new GeneralHandwritingOCRRequest();

            $params = $this->getParams();
            $req->fromJsonString(json_encode($params));

            $resp = $client->GeneralHandwritingOCR($req);

            return [true, $resp->toJsonString()];
        } catch (TencentCloudSDKException $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * 汽车车牌识别
     * @return array
     */
    public function licensePlateOCR(): array
    {
        try {

            $cred = new Credential($this->config->getSecretId(), $this->config->getSecretKey());
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->config->getEndpoint());

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new OcrClient($cred, $this->config->getRegion(), $clientProfile);

            $req = new LicensePlateOCRRequest();

            $params = $this->getParams();
            $req->fromJsonString(json_encode($params));

            $resp = $client->LicensePlateOCR($req);

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
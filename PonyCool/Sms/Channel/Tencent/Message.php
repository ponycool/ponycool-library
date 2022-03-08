<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/23
 * Time: 3:38 下午
 */
declare(strict_types=1);

namespace PonyCool\Sms\Channel\Tencent;

use PonyCool\Sms\SmsMessageInterface;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Sms\V20190711\SmsClient;
use Exception;

class Message implements SmsMessageInterface
{

    /**
     * 发送短信
     * @param array $phoneNumbers
     * @param string $templateId 模板ID
     * @param array|null $templateParam 模板参数
     * @return array
     */
    public function send(array $phoneNumbers, string $templateId, ?array $templateParam = null): array
    {
        $result = [
            'status' => false,
            'phoneNumbers' => $phoneNumbers,
            'templateId' => $templateId,
            'templateParam' => $templateParam,
        ];
        try {
            $credential = $this->getCredential();
            if (is_null($credential)) {
                throw new Exception('获取凭据失败');
            }
            $appid = getenv('sms.tencent.appId') ?: '';
            if (strlen($appid) === 0) {
                throw new Exception('未正确设置腾讯云AppID');
            }
            $sign = getenv('sms.tencent.sign');
            if (strlen($sign) === 0) {
                throw new Exception('未正确设置腾讯云短信服务签名');
            }
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($credential, "", $clientProfile);

            $req = new SendSmsRequest();

            $params = [
                "PhoneNumberSet" => $phoneNumbers,
                "TemplateID" => $templateId,
                "TemplateParamSet" => $templateParam,
                "SmsSdkAppid" => $appid,
                'Sign' => $sign
            ];
            $req->fromJsonString(json_encode($params));

            $resp = $client->SendSms($req);
            $resp = json_decode($resp->toJsonString(), true);

            $result = array_merge(
                $result,
                [
                    'status' => true,
                    'requestId' => $resp['RequestId'],
                    'sendResult' => $resp['SendStatusSet']
                ]
            );
        } catch (Exception $e) {
            $result = array_merge(
                $result,
                [
                    'status' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
        return $result;
    }

    /**
     * 获取凭据
     * @return Credential|null
     */
    public function getCredential(): ?Credential
    {
        $enabled = getenv('sms.tencent.enabled') ?: false;
        if ($enabled !== 'true') {
            return null;
        }
        $secretId = getenv('sms.tencent.secretId') ?: '';
        if (strlen($secretId) === 0) {
            return null;
        }
        $secretKey = getenv('sms.tencent.secretKey') ?: '';
        if (strlen($secretKey) === 0) {
            return null;
        }
        return new Credential($secretId, $secretKey);
    }
}
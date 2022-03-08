<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/16
 * Time: 2:14 下午
 */
declare(strict_types=1);

namespace PonyCool\Pay\Channel\Wechat;
require_once __DIR__ . '/lib/WxPay.Exception.php';
require_once __DIR__ . '/lib/WxPay.Api.php';

use WxPayApi;
use WxPayException;
use WxPayJsApiPay;

class JsApi
{
    /**
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array|null
     */
    public ?array $data = null;

    /**
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     * @param object|null $config
     * @return string
     */
    public function getOpenid(?object $config = null): string
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);
            $url = $this->createOauthUrlForCode($baseUrl, $config);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            echo $code;
            return $this->getOpenidFromMp($code, $config);
        }
    }

    /**
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     * @param object|null $config
     * @return string
     */
    public function getOpenidFromMp(string $code, ?object $config = null): string
    {
        $url = $this->createOauthUrlForOpenid($code, $config);

        //初始化curl
        $ch = curl_init();
        $curlVersion = curl_version();
        if (is_null($config)) {
            $config = new Config();
        }
        $ua = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlVersion['version'] . " "
            . $config->GetMerchantId();

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
        $config->GetProxy($proxyHost, $proxyPort);
        if ($proxyHost != "0.0.0.0" && $proxyPort != 0) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        return $data['openid'];
    }

    /**
     * 获取jsapi支付的参数
     * @param array $unifiedOrderResult $UnifiedOrderResult 统一支付接口返回的数据
     * @param object|null $config
     * @return string
     * @throws WxPayException
     */
    public function getJsApiParameters(array $unifiedOrderResult, ?object $config = null): string
    {
        if (!array_key_exists("appid", $unifiedOrderResult)
            || !array_key_exists("prepay_id", $unifiedOrderResult)
            || $unifiedOrderResult['prepay_id'] == "") {
            throw new WxPayException("参数错误");
        }

        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($unifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $unifiedOrderResult['prepay_id']);

        if (is_null($config)) {
            $config = new Config();
        }
        $jsapi->SetPaySign($jsapi->MakeSign($config));
        return json_encode($jsapi->GetValues());
    }

    /**
     * 获取地址js参数
     * @param object|null $config
     * @return string 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters(?object $config = null): string
    {
        if (is_null($config)) {
            $config = new Config();
        }
        $getData = $this->data;
        $data = array();
        $data["appid"] = $config->GetAppId();
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = WxPayApi::getNonceStr();
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $config->GetAppId(),
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        return json_encode($afterData);
    }

    /**
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     * @param object|null $config
     * @return string
     */
    private function createOauthUrlForCode(string $redirectUrl, ?object $config = null): string
    {
        if (is_null($config)) {
            $config = new Config();
        }
        $urlObj["appid"] = $config->GetAppId();
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return string 返回已经拼接好的字符串
     */
    private function ToUrlParams(array $urlObj): string
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 构造获取open和access_toke的url地址
     * @param string $code 微信跳转带回的code
     * @param object|null $config
     * @return string
     */
    private function createOauthUrlForOpenid(string $code, ?object $config = null): string
    {
        if (is_null($config)) {
            $config = new Config();
        }
        $urlObj["appid"] = $config->GetAppId();
        $urlObj["secret"] = $config->GetAppSecret();
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/15
 * Time: 4:39 下午
 */
declare(strict_types=1);

namespace PonyCool\Pay\Channel\Wechat;

require_once(__DIR__ . '/lib/WxPay.Config.Interface.php');

use WxPayConfigInterface;

class Config extends WxPayConfigInterface
{

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    /**
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     * @return array|false|string
     */
    public function GetAppId()
    {
        return getenv('pay.wechat.app.id');
    }

    /**
     * MCHID：商户号（必须配置，开户邮件中可查看）
     * @return array|false|string
     */
    public function GetMerchantId()
    {
        return getenv('pay.wechat.merchantId');
    }

    /**
     * 支付回调url
     * @return array|false|string
     */
    public function GetNotifyUrl()
    {
        return getenv('pay.wechat.notifyUrl');
    }

    /**
     * 签名和验证签名方式， 支持md5和sha256方式
     * @return string
     */
    public function GetSignType()
    {
        return 'HMAC-SHA256';
    }


    /**
     * 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @param $proxyHost
     * @param $proxyPort
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
    }

    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    public function GetReportLevenl()
    {
        return 1;
    }

    /**
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     * @return array|false|string
     */
    public function GetKey()
    {
        return getenv('pay.wechat.api.key');
    }

    /**
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @return array|false|string
     */
    public function GetAppSecret()
    {
        return getenv('pay.wechat.app.secret');
    }

    /**
     * 设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     * @param $sslCertPath
     * @param $sslKeyPath
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $sslCertPath = __DIR__ . '/cert/apiclient_cert.pem';
        $sslKeyPath = __DIR__ . '/cert/apiclient_key.pem';
    }
}

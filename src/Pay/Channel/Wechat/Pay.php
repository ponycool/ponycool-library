<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/15
 * Time: 2:58 下午
 */
declare(strict_types=1);

namespace PonyCool\Pay\Channel\Wechat;
require_once(__DIR__ . '/lib/WxPay.Api.php');
require_once(__DIR__ . '/lib/WxPay.Exception.php');
require_once(__DIR__ . '/lib/WxPay.Data.php');

use PonyCool\Pay\PayInterface;
use WxPayUnifiedOrder;
use WxPayApi;
use WxPayOrderQuery;
use Exception;
use WxPayException;

class Pay implements PayInterface
{

    // 交易类型
    private array $tradeType = [
        'JSAPI',
        'NATIVE',
        'APP',
        'MWEB',
    ];

    public function unifiedOrder(array $order, ?object $config = null): array
    {
        try {
            // 配置
            if (is_null($config)) {
                $config = new Config();
            }
            if (strlen($config->GetAppId() ?: '') === 0) {
                throw new Exception('微信支付未正确配置AppID');
            }
            if (strlen($config->GetAppSecret() ?: '') === 0) {
                throw new Exception('微信支付未正确配置AppSecret');
            }
            if (strlen($config->GetMerchantId() ?: '') === 0) {
                throw new Exception('微信支付未正确配置商户号');
            }
            if (strlen($config->GetNotifyUrl() ?: '') === 0) {
                throw new Exception('微信支付未正确配置支付回调地址');
            }
            $enableStatus = getenv('pay.wechat.enable') ?: 'false';
            if ($enableStatus !== 'true') {
                throw new Exception('微信支付未启用，请在配置中启用');
            }
            $unifiedOrder = new WxPayUnifiedOrder();
            if (strlen($order['desc'] ?? '') === 0) {
                throw new Exception('缺少参数商品简单描述desc');
            }
            $unifiedOrder->SetBody($order['desc']);
            if ($order['attach'] ?? false) {
                $unifiedOrder->SetAttach($order['attach']);
            }
            if (strlen($order['out_trade_no'] ?? '') === 0) {
                throw new Exception('缺少参数商户订单号out_trade_no');
            }
            $unifiedOrder->SetOut_trade_no($order['out_trade_no']);
            if (strlen((string)$order['total_fee'] ?? 0) === 0) {
                throw new Exception('缺少参数订单总金额total_fee');
            }
            $unifiedOrder->SetTotal_fee($order['total_fee']);
            $unifiedOrder->SetTime_start(date('YmdHis'));
            $unifiedOrder->SetTime_expire(date('YmdHis', time() + 60 * 60 * 2));
            if ($order['goods_tag'] ?? false) {
                $unifiedOrder->SetGoods_tag($order['goods_tag']);
            }
            if (strlen($order['trade_type'] ?? '') === 0) {
                throw new Exception('缺少参数交易类型trade_type');
            }
            if (!in_array($order['trade_type'], $this->tradeType, true)) {
                throw new Exception('无效的参数trade_type');
            }
            $unifiedOrder->SetTrade_type($order['trade_type']);
            // 回调地址
            $notifyUrl = getenv('pay.wechat.notifyUrl') ?: '';
            if (strlen($notifyUrl) === 0) {
                throw new Exception('缺少参数支付回调地址notify_url');
            }
            $unifiedOrder->SetNotify_url($notifyUrl);
            if (strlen($order['openid'] ?? '') === 0) {
                throw new Exception('缺少参数用户标识openid');
            }
            $unifiedOrder->SetOpenid($order['openid']);
            $order = WxPayApi::unifiedOrder($config, $unifiedOrder);
            if (($order['return_code'] ?? '') !== 'SUCCESS') {
                throw new Exception('创建预支付订单通信失败');
            }
            if (($order['result_code'] ?? '') !== 'SUCCESS') {
                throw new Exception('创建预支付订单失败');
            }
            return ['OK', $order];
        } catch (Exception $e) {
            return ['FAIL', $e->getMessage()];
        }
    }

    /**
     * 发起JsApi支付
     * @param array $order
     * @param object|null $config
     * @return array
     */
    public function jsApiPay(array $order, ?object $config = null): array
    {
        $jsApi = new JsApi();
        $order['trade_type'] = 'JSAPI';
        $pay = new Pay();
        $unifiedOrderResult = $pay->unifiedOrder($order, $config);
        if ($unifiedOrderResult[0] !== 'OK') {
            return $unifiedOrderResult;
        }
        try {
            $res = $jsApi->getJsApiParameters($unifiedOrderResult[1], $config);
            return ['OK', $res];
        } catch (WxPayException $e) {
            return ['FAIL', $e->getMessage()];
        }
    }

    /**
     * 查询订单
     * @param string $tradeNo 交易编号
     * @param object|null $config
     * @return array
     */
    public function queryOrder(string $tradeNo, ?object $config = null): array
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($tradeNo);
        if (is_null($config)) {
            $config = new Config();
        }
        try {
            $result = WxPayApi::orderQuery($config, $input);
            if (!is_array($result)) {
                throw new Exception('查询订单结果异常');
            }
            if (array_key_exists("return_code", $result)
                && array_key_exists("result_code", $result)
                && $result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS") {
                return [true, $result];
            }
            return [false, '查询订单失败'];
        } catch (WxPayException $e) {
            return [false, '查询订单失败，error：' . $e->getMessage()];
        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * 检查签名
     * @param array $rawData
     * @param object|null $config
     * @return array|bool[]
     */
    public function checkSign(array $rawData, ?object $config = null): array
    {
        if (is_null($config)) {
            $config = new Config();
        }
        try {
            //签名步骤一：按字典序排序参数
            ksort($rawData);
            $str = $this->ToUrlParams($rawData);
            //签名步骤二：在string后加入KEY
            $str .= "&key=" . $config->GetKey();
            //签名步骤三：MD5加密或者HMAC-SHA256
            if ($config->GetSignType() == "MD5") {
                $string = md5($str);
            } else if ($config->GetSignType() == "HMAC-SHA256") {
                $string = hash_hmac("sha256", $str, $config->GetKey());
            } else {
                throw new WxPayException("签名类型不支持！");
            }
            //签名步骤四：所有字符转为大写
            $sign = strtoupper($string);
            if ($sign !== $rawData['sign']) {
                throw new WxPayException('签名校验失败');
            }
            return [true];
        } catch (WxPayException $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * 格式化参数格式化成url参数
     * @param $params
     * @return string
     */
    public function ToUrlParams($params): string
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        return trim($buff, "&");
    }

    /**
     * 输出xml字符
     * @param array $data
     * @return string
     * @throws WxPayException
     */
    public function ToXml(array $data): string
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new WxPayException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}

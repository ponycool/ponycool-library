<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/5
 * Time: 4:20 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\Ocr;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Aliyun implements OcrInterface
{

    private Config $config;


    /**
     * @param Config $config
     * @return bool
     */
    public function checkConfig(Config $config): bool
    {
        if (empty($config->getAppCode())) {
            return false;
        }
        $this->config = $config;
        return true;
    }

    /**
     * @return array|mixed
     */
    public function generalBasicOCR(): array
    {
        try {
            $host = "https://tysbgpu.market.alicloudapi.com";
            $path = "/api/predict/ocr_general";
            $method = "POST";
            $appcode = $this->config->getAppCode();
            $headers = [
                'Authorization' => 'APPCODE ' . $appcode,
                'Content-Type' => 'application/json; charset=UTF-8'
            ];
            $body = $this->getParams();
            $url = $host . $path;
            $options = [
                'body' => $body,
            ];
            $client = new Client(
                [
                    'headers' => $headers,
                    'verify' => false,
                ]
            );
            $response = $client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new Exception('阿里云印刷文字识别-通用文字识别接口调用失败');
            }

            $res = $response->getBody()
                ->getContents();
            return [true, $res];
        } catch (GuzzleException $e) {
            return [false, $e->getMessage()];
        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }
    }

    /**
     * @return array
     */
    public function generalHandwritingOCR(): array
    {
        // TODO: Implement generalHandwritingOCR() method.
    }

    /**
     * @return array
     */
    public function licensePlateOCR(): array
    {
        // TODO: Implement licensePlateOCR() method.
    }

    /**
     * 获取参数
     * @return string
     */
    private function getParams(): string
    {
        $params = [
            // 图片二进制数据的base64编码/图片url
            "image" => "",
            "configure" => [
                // 图片中文字的最小高度，单位像素
                "min_size" => 16,
                // 是否输出文字框的概率
                "output_prob" => true,
                // 是否输出文字框角点
                "output_keypoints" => false,
                // 是否跳过文字检测步骤直接进行文字识别
                "skip_detection" => false,
                // 是否关闭文字行方向预测
                "without_predicting_direction" => false
            ]
        ];
        if (!is_null($this->config->getImageUrl())) {
            $params = array_merge($params, ['image' => $this->config->getImageUrl()]);
        }
        return json_encode($params, JSON_UNESCAPED_SLASHES);
    }
}
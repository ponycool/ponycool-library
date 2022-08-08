<?php
declare(strict_types=1);

namespace PonyCool\Core\Client;

class Client
{
    private string $IP;

    /**
     * @param bool $adv
     * @return string
     */
    public function getIP(bool $adv = true): string
    {
        $this->getClientIP(0, $adv);
        return $this->IP;
    }

    /**
     * @param mixed $IP
     */
    public function setIP(string $IP): void
    {
        $this->IP = $IP;
    }

    /**
     * 获取客户端IP
     * @param int $type 返回类型：0返回IP地址，1返回IPV4地址数字
     * @param bool $adv 是否开启高级模式
     */
    private function getClientIP(int $type = 0, bool $adv = false): void
    {
        $type = $type ?? 0;
        $ip = null;
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        $this->setIP($ip[$type]);
    }
}

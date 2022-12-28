<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/15
 * Time: 2:50 下午
 */
declare(strict_types=1);

namespace PonyCool\Pay;

interface PayInterface
{
    /**
     * 统一下单
     * @param array $order
     * @param object|null $config
     * @return array
     */
    public function unifiedOrder(array $order, ?object $config = null): array;

    /**
     * 查询订单
     * @param string $tradeNo 交易编号
     * @param object|null $config
     * @return array
     */
    public function queryOrder(string $tradeNo, ?object $config = null): array;
}

<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/5
 * Time: 4:13 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Result;

class Result
{

    /**
     * 结果数据
     * @param array|null $res
     * @return array
     */
    public static function data(?array $res): array
    {
        if (is_null($res)) {
            return [false, null];
        }
        list($status, $data) = $res;
        if ($res[0] === true) {
            if ($res[1]['status'] !== 0) {
                $status = false;
                $data = $res[1]['message'];
            }
        }
        return [$status, $data];
    }
}
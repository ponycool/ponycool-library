<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/3/16
 * Time: 10:07
 */
declare(strict_types=1);

namespace PonyCool\Core;

class ArrayUtil
{
    /**
     * 获取字符串的所有排列组合数组
     * @param string $str
     * @return array
     */
    public static function getCombination(string $str): array
    {
        $exhaustive = function ($str = '', &$comb = []) use (&$exhaustive) {
            if (trim($str) == '' || !$str) return false;
            if (strlen($str) <= 1) {
                $comb[] = $str;
            } else {
                $str_first = $str[0];
                $comb_temp = $exhaustive(substr($str, 1), $comb);
                $comb[] = $str_first;
                foreach ($comb_temp as $k => $v) {
                    $comb[] = $str_first . $v;
                }
            }
            return $comb;
        };
        $combination = $exhaustive($str);
        sort($combination);
        return $combination;
    }

    /**
     * Obj转数组
     * @param $array
     * @return array
     */
    public static function objectToArray($array): array
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = self::objectToArray($value);
            }
        }
        return $array;
    }

    /**
     * 数组去除空格
     * @param array $arr
     * @return array
     */
    public static function arrayTrim(array $arr): array
    {
        $trim = function ($input) {
            if (!is_array($input))
                return trim($input);
            return array_map('trim', $input);
        };
        return $trim($arr);
    }

    /**
     * 二维数组排序
     * @param array $array 待排序数组
     * @param string $field 排序字段
     * @param string $sort 排序
     * @return array
     */
    public static function arraySort(array $array, string $field, string $sort = 'ASC'): array
    {
        $fields = [];
        foreach ($array as $item) {
            $fields[] = $item[$field];
        }
        if ('ASC' == strtoupper($sort)) {
            $sort = SORT_ASC;
        } elseif ('DESC' == strtoupper($sort)) {
            $sort = SORT_DESC;
        }
        array_multisort($fields, $sort, $array);
        return $array;
    }

    /**
     * 笛卡尔乘积
     * 接收一个包含多个数组的参数，每个数组代表一个维度的选项。函数会返回一个包含所有可能组合的二维数组
     * 示例用法
     * $arrayLib=new ArrayLib();
     * $colors = array('红色', '蓝色', '黑色');
     * $sizes = array('S', 'M', 'L');
     * $cartesian = $arrayLib::cartesian(array($colors, $sizes));
     * @param array $arrays
     * @return array
     */
    public static function cartesian(array $arrays): array
    {
        $result = array();
        $arrays = array_values($arrays);
        $sizeIn = sizeof($arrays);
        $size = $sizeIn > 0 ? 1 : 0;
        foreach ($arrays as $array) {
            $size = $size * sizeof($array);
        }
        for ($i = 0; $i < $size; $i++) {
            $result[$i] = array();
            for ($j = 0; $j < $sizeIn; $j++) {
                $result[$i][] = current($arrays[$j]);
            }
            for ($j = ($sizeIn - 1); $j >= 0; $j--) {
                if (next($arrays[$j])) {
                    break;
                } elseif (isset ($arrays[$j])) {
                    reset($arrays[$j]);
                }
            }
        }
        return $result;
    }
}
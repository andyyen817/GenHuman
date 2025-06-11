<?php
// +----------------------------------------------------------------------
// | 贵州猿创科技 [致力于通过产品和服务，帮助创业者高效化开拓市场]
// +----------------------------------------------------------------------
// | Copyright(c)2019~2024 https://xhadmin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 这不是一个自由软件，不允许对程序代码以任何形式任何目的的再发行
// +----------------------------------------------------------------------
// | Author:贵州猿创科技<416716328@qq.com>|<Tel:18786709420>
// +----------------------------------------------------------------------

namespace app\common\providers;

use app\model\Config;
use support\Redis;


class ConfigProviders
{
    /**
     * 获取配置
     * @param string $name
     * @return array|mixed
     */
    public static function get($name = '', $field = null)
    {
        if (!$name) {
            return [];
        }
        $value = self::getValue($name);
        if ($field != null) {
            return self::getValueByPath($value, $field);
        }
        return $value;
    }


    private static function getValue($name)
    {
        $data = Redis::get(self::getKey($name));
        if ($data) {
            return json_decode($data, true);
        }
        $config = Config::where('key', $name)->find();
        if (!$config) {
            return [];
        }
        Redis::set(self::getKey($name), json_encode($config->value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $config->value;
    }


    private static function getValueByPath(array $arr, string $keyPath)
    {
        $keys = explode('.', $keyPath);

        foreach ($keys as $key) {
            if (!is_array($arr) || !array_key_exists($key, $arr)) {
                return null;
            }
            $arr = $arr[$key];
        }

        return $arr;
    }


    public static function set($name, $value)
    {
        if (!$value) {
            return;
        }
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        Redis::set(self::getKey($name), $value);
    }


    private static function getKey($name)
    {
        return strtoupper('XCYD:' . $name);
    }

}
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
namespace app\common\utils;


trait JsonUtil
{
    public static function json(int $code = 200, string $msg = '', mixed $data = [], mixed $other = [])
    {
        return json(compact('code', 'msg', 'data', 'other'));
    }

    /**
     * 成功返回
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-20
     */
    public function success($msg = '请求成功', $code = 200)
    {
        return self::json($code, $msg);
    }
    /**
     * 失败返回
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-20
     */
    public function error($msg = '请求失败', $code = 400)
    {
        return self::json($code, $msg);
    }

    /**
     * 成功返回数据
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function successData($data = [], $msg = '请求成功', $code = 200)
    {
        return self::json($code, $msg, $data);
    }


    /**
     * 失败返回数据
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function errorData($data = [], $msg = '请求失败', $code = 400)
    {
        return self::json($code, $msg, $data);
    }
}
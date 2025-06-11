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

namespace app\logic;

use app\common\providers\ConfigProviders;
use app\model\Bill;
use app\model\User;
use GuzzleHttp\Client;

class BillLogic
{

    /**
     * 用户账单记录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-28
     */
    public static function Bill($uid, $type, $number, $remarks = '')
    {
        if ($number <= 0) {
            return;
        }
        if(!is_numeric($uid)){
            return;
        }
        $billArr = [
            'uid' => $uid,
            'type' => $type,
            'number' => $number,
            'remarks' => $remarks,
        ];

        $model = new Bill();
        $model->save($billArr);
        if ($type == 1) {
            User::where('id', $uid)->setDec('points', $number);
        }
        if ($type == 2) {
            User::where('id', $uid)->setInc('points', $number);
        }
    }

}
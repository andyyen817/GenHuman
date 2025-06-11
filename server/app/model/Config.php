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

namespace app\model;

use app\common\providers\ConfigProviders;
use think\Model;

class Config extends Model
{

    public function getValueAttr($value)
    {
        return json_decode($value, true);
    }
    public function setValueAttr($value)
    {
        return json_encode($value);
    }

    public function onAfterInsert($model)
    {
        ConfigProviders::set($model->key, $model->value);
    }

    public function onAfterUpdate($model)
    {
        ConfigProviders::set($model->key, $model->value);
    }
}
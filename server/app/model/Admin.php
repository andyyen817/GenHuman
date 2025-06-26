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

use app\common\providers\UploadProviders;
use think\Model;


class Admin extends Model
{

    protected $hidden = ['password'];
    public function setAvatarAttr($value)
    {
        if (empty($value)) {
            return '';
        }
        return UploadProviders::path($value);
    }

    public function getAvatarAttr($value)
    {
        if (empty($value)) {
            return '';
        }
        if (strpos($value, 'static/logo.svg') === 0) {
            return '//' . request()->host() . '/' . $value;
        }
        return UploadProviders::url($value);
    }



}
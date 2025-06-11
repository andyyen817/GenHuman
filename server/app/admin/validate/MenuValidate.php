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

namespace app\admin\validate;

use think\Validate;

class MenuValidate extends Validate
{

    protected $rule = [
        'title' => 'require',
        'path' => 'require|unique:menu',
        'sort' => 'require|integer',
    ];

    protected $message = [
        'title.require' => '菜单名称不能为空',
        'path.require' => '菜单路径不能为空',
        'sort.require' => '排序不能为空',
        'sort.integer' => '排序必须是整数',
        'path.unique' => '菜单路径已存在',
    ];

}
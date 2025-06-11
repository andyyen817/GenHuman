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

namespace app\controller;

use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $path = public_path(path: '/h5/index.html');
        return response(file_get_contents($path));
    }


    public function h5(Request $request)
    {
        $path = public_path(path: '/h5/index.html');
        return response(file_get_contents($path));
    }
}

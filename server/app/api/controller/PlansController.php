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

namespace app\api\controller;

use app\common\BaseController;
use app\common\providers\ConfigProviders;
use app\model\Plans;
use app\model\Scene;
use app\model\User;
use app\service\YiDingService;
use support\Request;
use think\facade\Db;

class PlansController extends BaseController
{

    /**
     * 获取套餐
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-03
     */
    public function getList()
    {
        $list = Plans::where('status', 1)
            ->order('sort desc')
            ->select();
        return $this->successData($list);
    }

}
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
use app\logic\BillLogic;
use app\model\Order;
use app\model\Plans;
use app\model\User;
use app\service\WechatPayService;
use app\service\YiDingService;
use support\Request;

class CopyWritingController extends BaseController
{

    /**
     * 文案
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function createCw(Request $request)
    {
        $title = $request->post('title', '');
        if (!$title) {
            return $this->error('标题不能为空');
        }
        $aiCwPoint = ConfigProviders::get('created', 'aiCwPoint');
        $user = User::where('id', $request->uid)->find();
        if ($user->points < $aiCwPoint) {
            return $this->error('算力点不足');
        }
        $size = $request->post('size', 100);
        $service = new YiDingService();
        $data = $service->copywrite($title, $size);
        if (!$data) {
            return $this->error('文案生成失败');
        }

        BillLogic::Bill($request->uid, 1, $aiCwPoint, '文案生成消耗');
        return $this->successData($data);
    }

}
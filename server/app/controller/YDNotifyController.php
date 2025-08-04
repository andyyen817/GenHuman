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

use app\common\BaseController;
use app\common\providers\ConfigProviders;
use app\common\providers\UploadProviders;
use app\common\trait\UploadTrait;
use app\logic\BillLogic;
use app\model\Scene;
use app\model\Works;
use support\Log;
use support\Request;
use think\facade\Db;

class YDNotifyController extends BaseController
{
    use UploadTrait;
    /**
     * 分身克隆回调
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */
    public function scene(Request $request)
    {
        $data = $request->all();
        Log::info('分身克隆回调', $data);
        if (!isset($data['code']) || !isset($data['data']['scene_task_id'])) {
            return false;
        }
        Db::startTrans();
        try {
            $model = Scene::where('task_id', $data['data']['scene_task_id'])->find();
            if (!$model) {
                return false;
            }
            $createConfig = ConfigProviders::get('created');
            if ($data['code'] == 200) {
                $model->status = 2;
                $model->scene_id = $data['data']['sceneId'];
                //把图片保存到本地
                $coverUrl = UploadProviders::saveRemoteFile($data['data']['coverUrl'], $model->uid);
                $model->cover = $coverUrl;
                BillLogic::Bill($model->uid, 1, $createConfig['scenePoint'], '分身克隆');
            } else {
                $model->status = 3;
            }
            $model->save();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            Log::error('分身克隆回调失败', ['error' => $e->getMessage()]);
            return false;
        }
        return true;
    }

    /**
     * 视频合成回调
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */

    public function video(Request $request)
    {
        $data = $request->all();
        Log::info('视频合成回调', $data);
        if (!isset($data['code']) || !isset($data['data']['video_task_id'])) {
            return false;
        }
        Db::startTrans();
        try {
            $model = Works::where('task_id', $data['data']['video_task_id'])->find();
            if (!$model) {
                return false;
            }
            $createConfig = ConfigProviders::get('created');
            if ($data['code'] == 200) {
                $model->status = 2;
                $coverUrl = UploadProviders::saveRemoteFile($data['data']['coverUrl'], $model->uid);
                $model->cover = $coverUrl;
                $model->video_url = $data['data']['videoUrl'];
                $model->duration = $data['data']['duration'];
                $points = ceil($data['data']['duration']) * $createConfig['worksPoint'];
                BillLogic::Bill($model->uid, 1, $points, '视频合成');
            } else {
                $model->status = 3;
            }
            $model->save();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
        }
    }
}

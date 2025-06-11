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
use app\model\User;
use app\model\Voice;
use app\service\YiDingService;
use support\Request;
use think\facade\Db;

class VoiceController extends BaseController
{

    /**
     * 添加语音
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-27
     */
    public function addVoice(Request $request)
    {
        $data = $request->post();
        $service = new YiDingService();
        $voicePoint = ConfigProviders::get('created', 'voicePoint') ?? 0;
        $voiceChannel = $request->post('voiceChannel', 1);
        $user = User::where('id', $request->uid)->find();
        if ($user->points < $voicePoint) {
            return $this->error('算力点不足');
        }
        Db::startTrans();
        try {
            $result = $service->cloneVoice($data['title'], $data['url'], $voiceChannel);
            $model = new Voice();
            $model->uid = $request->uid;
            $model->task_id = $result['task_id'] ?? '';
            $model->task_id = $result['task_id'];
            $model->voice_id = $result['voice_id'];
            $model->status = 2;
            $model->duration = $data['duration'];
            $model->voice_url = $data['url'];
            $model->title = $data['title'];
            $model->save();
            if ($voicePoint > 0) {
                BillLogic::Bill($request->uid, 1, $voicePoint, '语音克隆');
            }
            Db::commit();
            return $this->success('克隆成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->error('克隆失败:' . $e->getMessage());
        }
    }


    /**
     * 获取语音列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-28
     */
    public function getList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $data = $request->get();
        $where = [];
        if (isset($data['type'])) {
            $where[] = ['status', '=', $data['type']];
        }
        $where[] = ['uid', '=', $request->uid];
        $list = Voice::where($where)
            ->order('id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();
        return $this->successData($list);
    }


    /**
     * 修改名称
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */
    public function editName(Request $request)
    {
        $data = $request->post();
        $voice = Voice::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$voice) {
            return $this->error('音色不存在');
        }
        if (empty($data['title'])) {
            return $this->error('名称不能为空');
        }
        $voice->title = $data['title'];
        if ($voice->save()) {
            return $this->success('修改成功');
        }
        return $this->error('修改失败');
    }


    /**
     * 删除
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */
    public function delete(Request $request)
    {
        $data = $request->post();
        $voice = Voice::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$voice) {
            return $this->error('音色不存在');
        }
        if ($voice->delete()) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
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
use app\model\Scene;
use app\model\User;
use app\model\Voice;
use app\model\Works;
use app\service\YiDingService;
use support\Request;
use think\facade\Db;

class WorksController extends BaseController
{
    /**
     * 创建视频
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-28
     */
    public function addWorks(Request $request)
    {
        $data = $request->post();
        $service = new YiDingService();
        $config = ConfigProviders::get('created');

        $user = User::where('id', $request->uid)->find();

        if ($user->points < $config['worksPoint']) {
            return $this->error('算力点不足');
        }

        $scene = Scene::where('id', $data['scene_id'])
            ->where('status', 2)
            ->find();
        if (!$scene) {
            return $this->error('场景不存在');
        }
        $voice = Voice::where('id', $data['voice_id'])
            ->where('status', 2)
            ->find();
        if (!$voice) {
            return $this->error('音色不存在');
        }

        //根据文字生成音频
        $audio_url = $service->createAudio($voice->voice_id, $data['text'], $voice->channel);

        $worksChannel = ConfigProviders::get('yiding', 'worksChannel');
        $task_id = $service->createVideo($scene->task_id, $audio_url, $worksChannel);
        if (!$task_id) {
            return $this->error('视频生成失败');
        }
        $model = new Works();

        $model->task_id = $task_id;
        $model->uid = $request->uid;
        $model->title = $data['title'];
        $model->audio_url = $audio_url;
        $model->scene_id = $scene->id;
        $model->voice_id = $voice->id;
        $model->channel = $worksChannel;
        $model->status = 1; //生成中
        if ($model->save()) {
            return $this->success('创建任务成功');
        }
        return $this->error('创建任务失败');
    }


    /**
     * 获取视频列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */
    public function getList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $data = $request->get();
        $where = [];
        $where[] = ['uid', '=', $request->uid];
        if (isset($data['type']) && $data['type'] != 0) {
            $where[] = ['status', '=', $data['type']];
        }
        $list = Works::where($where)
            ->order('id', 'desc')
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
        $model = Works::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$model) {
            return $this->error('任务不存在');
        }
        if (empty($data['title'])) {
            return $this->error('名称不能为空');
        }
        $model->title = $data['title'];
        if ($model->save()) {
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
        $model = Works::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$model) {
            return $this->error('任务不存在');
        }
        if ($model->delete()) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
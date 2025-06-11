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
use app\service\YiDingService;
use support\Request;
use think\facade\Db;

class SceneController extends BaseController
{

    /**
     * 分身克隆
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-28
     */
    public function addScene(Request $request)
    {
        $data = $request->post();
        $service = new YiDingService();
        $createConfig = ConfigProviders::get('created');
        if (!$createConfig) {
            return $this->error('请先完成配置');
        }
        $user = User::where('id', $request->uid)->find();
        if ($user->points < $createConfig['scenePoint']) {
            return $this->error('算力点不足');
        }

        Db::startTrans();
        try {
            $task_id = $service->createScene($data['title'], $data['url']);
            $model = new Scene();
            $model->title = $data['title'];
            $model->uid = $request->uid;
            $model->task_id = $task_id;
            $model->video_url = $data['url'];
            $model->status = 1;
            $model->save();
            Db::commit();
            return $this->success('克隆成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->error('克隆失败:' . $e->getMessage());
        }
    }

    /**
     * 获取列表
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

        $list = Scene::where($where)
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
        $model = Scene::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$model) {
            return $this->error('任务不存在');
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
        $model = Scene::where('id', $data['id'])
            ->where('uid', $request->uid)
            ->find();
        if (!$model) {
            return $this->error('任务不存在');
        }
        if ($model->status == 1) {
            return $this->error('任务正在进行中，无法删除');
        }
        if ($model->delete()) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
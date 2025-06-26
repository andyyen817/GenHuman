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

namespace app\admin\controller;

use app\common\BaseController;
use app\model\App;
use support\Request;

class AppController extends BaseController
{
    /**
     * 获取智能应用
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getList(Request $request)
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $list = App::order('id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        return $this->successData($list);
    }


    /**
     * 添加或编辑应用
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function saveApp(Request $request)
    {
        $data = $request->post();
        if (isset($data['id']) && $data['id']) {
            $app = App::find($data['id']);
            if (!$app) {
                return $this->error('应用不存在');
            }
        } else {
            $app = new App();
        }
        $app->title = $data['title'];
        $app->sub_title = $data['sub_title'] ?? '';
        $app->image = $data['image'] ?? '';
        $app->status = $data['status'] ?? 1;
        $app->sort = $data['sort'] ?? 0;
        $app->type = $data['type'];
        $app->points = $data['points'] ?? 0;
        $app->tableData = $data['tableData'] ?? '';
        $app->role_instruct = $data['role_instruct'] ?? '';
        $app->content_instruct = $data['content_instruct'] ?? '';
        $app->save();
        return $this->success('保存成功');
    }


    /**
     * 获取应用详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->error('缺少应用ID');
        }
        $app = App::find($id);
        if (!$app) {
            return $this->error('应用不存在');
        }
        return $this->successData($app);
    }


    /**
     * 删除
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function delete(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return $this->error('缺少应用ID');
        }
        $app = App::find($id);
        if (!$app) {
            return $this->error('应用不存在');
        }
        $app->delete();
        return $this->success('删除成功');
    }

    /**
     * 获取选项
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function getOptions(Request $request)
    {
        $list = App::order('sort asc')->where('status', 1)->field('id as value,title as label')->select();
        return $this->successData($list);
    }
}
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
use app\model\Upload;
use support\Request;

class FileController extends BaseController
{

    /**
     * 获取文件列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-23
     */
    public function getFileList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 100);
        $fileType = (int) $request->get('fileType', 0);
        $name = $request->get('title', '');
        $where = [];
        if ($fileType != 0) {
            $where[] = ['type', '=', $fileType];
        }
        if ($name != '') {
            $where[] = ['title', 'like', '%' . $name . '%'];
        }

        $list = Upload::where($where)
            ->order('id', 'desc')->paginate([
                    'page' => $page,
                    'list_rows' => $limit,
                ]);
        return $this->successData($list);
    }

    /**
     * 修改名称
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-23
     */
    public function editName(Request $request)
    {
        $id = (int) $request->post('id', 0);
        $name = $request->post('name', '');
        if ($id == 0) {
            return $this->error('参数错误');
        }
        if ($name == '') {
            return $this->error('名称不能为空');
        }
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->error('文件不存在');
        }
        if ($model->save(['title'=>$name])) {
            return $this->success('修改成功');
        } else {
            return $this->error('修改失败');
        }
    }

    /**
     * 删除文件
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-23
     */
    public function delFile(Request $request)
    {
        $id = (int) $request->post('id', 0);
        if ($id == 0) {
            return $this->error('参数错误');
        }
        $model = Upload::where('id', $id)->find();
        if (!$model) {
            return $this->error('文件不存在');
        }
        if ($model->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * 批量删除
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-23
     */
    public function delFiles(Request $request)
    {
        $ids = $request->post('ids', []);
        if (empty($ids)) {
            return $this->error('参数错误');
        }
        $model = Upload::whereIn('id', $ids)->select();
        if (!$model) {
            return $this->error('文件不存在');
        }
        if ($model->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}
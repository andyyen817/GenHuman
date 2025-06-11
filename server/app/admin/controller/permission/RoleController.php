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

namespace app\admin\controller\permission;

use app\admin\validate\RoleValidate;
use app\common\BaseController;
use app\model\Role;
use support\Request;

class RoleController extends BaseController
{

    /**
     * 获取角色列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function getRoleList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $roleList = Role::order('id desc')
            ->paginate([
                'page' => $page,
                'list_rows' => $limit,
            ]);
        return $this->successData($roleList);
    }

    /**
     * 编辑角色
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function saveRole(Request $request)
    {
        $data = $request->post();
        xcValidate(RoleValidate::class, $data);
        if (isset($data['id']) && $data['id']) {
            $model = Role::where('id', $data['id'])->find();
        } else {
            $model = new Role();
        }
        if ($model->save($data)) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }


    /**
     * 编辑角色权限
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function editRoleMenu(Request $request)
    {
        $data = $request->post();
        $model = Role::where('id', $data['id'])->find();
        if (!$model) {
            return $this->error('角色不存在');
        }
        if ($model->save($data)) {
            return $this->success('编辑成功');
        }
        return $this->error('编辑失败');
    }

    /**
     * 获取角色详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function getRoleDetail(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->error('参数错误');
        }
        $role = Role::where('id', $id)->find();
        if (!$role) {
            return $this->error('角色不存在');
        }
        return $this->successData($role);
    }

    /**
     * 删除角色
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function deleteRole(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return $this->error('参数错误');
        }
        $role = Role::where('id', $id)->find();
        if (!$role) {
            return $this->error('角色不存在');
        }
        if ($role->delete()) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
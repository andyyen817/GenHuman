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

use app\admin\validate\AdminValidate;
use app\common\BaseController;
use app\model\Admin;
use app\model\Role;
use support\Request;
use Tinywan\Jwt\JwtToken;
use app\model\Menu;

class UserController extends BaseController
{
    /**
     * 获取用户列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function getUserList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $userList = Admin::order('id desc')
            ->paginate([
                'page' => $page,
                'list_rows' => $limit,
            ]);
        return $this->successData($userList);
    }

    /**
     * 编辑用户
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function saveUser(Request $request)
    {
        $data = $request->post();
        xcValidate(AdminValidate::class, $data);
        if (isset($data['id']) && $data['id']) {
            $model = Admin::where('id', $data['id'])->find();
        } else {
            $model = new Admin();
        }
        $model->username = $data['username'];
        $model->nickname = $data['nickname'];
        $model->avatar = $data['avatar'] ?? '';
        $model->status = $data['status'];
        if (isset($data['password']) && $data['password']) {
            $model->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if ($model->save()) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }

    /**
     * 获取用户详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function getUserDetail(Request $request)
    {
        $id = $request->get('id');
        $user = Admin::where('id', $id)->find();
        if (!$user) {
            return $this->error('用户不存在');
        }
        return $this->successData($user);
    }



    protected $noLogin = [
        'login',
    ];

    /**
     * 用户登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function login(Request $request)
    {
        $data = $request->post();
        $user = Admin::where('username', $data['username'])->find();
        if (!$user) {
            return $this->error('用户不存在');
        }

        if (!password_verify($data['password'], $user->password)) {
            return $this->error('密码错误');
        }
        $token = JwtToken::generateToken([
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'access_exp' => 86400 * 7
        ]);
        return $this->successData([
            'token' => $token['access_token'],
        ]);
    }

    /**
     * 获取用户信息
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function getUserInfo(Request $request)
    {
        $user = Admin::where('id', $request->uid)->find();
        if (!$user) {
            return $this->error('用户不存在');
        }
        $permissions = ['*:*:*'];
        return $this->successData([
            'id' => $user->id,
            'nickname' => $user->nickname,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'status' => $user->status,
            'permissions' => $permissions
        ]);
    }

    /**
     * 获取权限菜单
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function getUserRoutes(Request $request)
    {
        $admin = Admin::where('id', $request->uid)->find();
        if ($admin->is_system == 1) {
            $menusList = Menu::where('status', 1)->select();
        } else {
            $menu_ids = Role::where('id', $admin->role_id)->value('menu_ids');
            if (!$menu_ids) {
                return $this->error('没有权限');
            }
            $menusList = Menu::whereIn('id', explode(',', $menu_ids))->where('status', 1)->select();
        }
        $menus = buildTree($menusList->toArray());
        return $this->successData($menus);
    }


    /**
     * 退出登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-06
     */
    public function logout()
    {
        try {
            JwtToken::clear();
            return $this->success('退出成功');
        } catch (\Exception $e) {
            return $this->error('退出失败: ' . $e->getMessage());
        }
    }
}
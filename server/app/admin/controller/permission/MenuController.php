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

use app\admin\validate\MenuValidate;
use app\common\BaseController;
use app\model\Menu;
use support\Request;

class MenuController extends BaseController
{
    /**
     * 获取菜单列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function getMenuList(Request $request)
    {
        $menusList = Menu::select();
        $menus = buildTree($menusList->toArray());
        return $this->successData($menus);
    }


    /**
     * 添加菜单
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-21
     */
    public function saveMenu(Request $request)
    {
        $data = $request->post();
        xcValidate(MenuValidate::class, $data);
        if (isset($data['id']) && $data['id']) {
            $model = Menu::where('id', $data['id'])->find();
        } else {
            $model = new Menu();
        }
        if ($model->save($data)) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }

    /**
     * 获取菜单详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function getMenuDetail(Request $request)
    {
        $id = $request->get('id');
        $menu = Menu::where('id', $id)->find();
        if (!$menu) {
            return $this->error('菜单不存在');
        }
        return $this->successData($menu);
    }
}
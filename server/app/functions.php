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
/**
 * Here is your custom functions.
 */

function xcValidate($class, $data, $scene = null)
{
    $validate = new $class();
    if ($scene) {
        $validate->scene($scene);
    }
    if (!$validate->check($data)) {
        throw new \think\exception\ValidateException($validate->getError());
    }
}


function buildTree(array $items, $parentId = 0): array
{
    $tree = [];
    foreach ($items as $item) {
        if ($item['parentId'] == $parentId) {
            // 递归查找子节点
            $children = buildTree($items, $item['id']);
            if (!empty($children)) {
                $item['children'] = $children;
            }
            $item['activeMenu'] = '';
            $tree[] = $item;
        }
    }
    return $tree;
}

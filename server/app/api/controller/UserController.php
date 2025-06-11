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
use app\service\AppletService;
use app\service\OfficialService;
use support\Request;
use Tinywan\Jwt\JwtToken;

class UserController extends BaseController
{
    protected $noLogin = ['login', 'officialLogin', 'wechatCallback'];
    /**
     * 登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-24
     */
    public function login(Request $request)
    {
        $data = $request->post();
        $code = $data['code'] ?? '';
        $pid = $data['pid'] ?? 0;
        if (!$code) {
            return $this->error('code不能为空');
        }
        $appletService = new AppletService();
        $result = $appletService->login($code);
        if (!isset($result['openid'])) {
            return $this->error('登录失败，请重试');
        }
        $userInfo = null;
        if (isset($result['unionid']) && $result['unionid']) {
            $userInfo = User::where('unionid', $result['unionid'])->find();
        }
        if (!$userInfo) {
            $userInfo = User::where('openid', $result['openid'])->find();
        }
        if (!$userInfo) {
            $userInfo = $this->addUser($result['openid'], $result['unionid'] ?? '', $pid, 'applet');
        }
        $token = $this->getToken($userInfo);
        $user = [
            'id' => $userInfo->id,
            'nickname' => $userInfo->nickname,
            'avatar' => $userInfo->avatar,
            'points' => $userInfo->points,
            'mobile' => $userInfo->mobile,
            'balance' => $userInfo->balance,
        ];
        return $this->successData(compact('token', 'user'));
    }

    /**
     * 公众号登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function officialLogin(Request $request)
    {
        $pid = $request->post('pid', 0);
        $page = $request->post('path', 'pages/index/index');
        if (!$request->isWehcatBrowser) {
            return $this->error('请在微信浏览器中打开');
        }
        $wxConfig = ConfigProviders::get('official');
        if (empty($wxConfig['appid']) || empty($wxConfig['secret'])) {
            return $this->error('请先完成公众号配置');
        }
        $domain = ConfigProviders::get('site', 'webUrl');
        $query = [
            'appid' => $wxConfig['appid'],
            'redirect_uri' => "{$domain}/api/User/wechatCallback?&user_id={$pid}&page={$page}",
            'response_type' => 'code',
            'scope' => 'snsapi_userinfo',
        ];
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize";
        $url = $url . '?' . http_build_query($query) . "#wechat_redirect";
        return $this->successData($url);
    }

    /**
     * 登录回调
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function wechatCallback(Request $request)
    {
        try {
            // 获取请求数据
            $data = $request->get();
            $pid = $data['pid'] ?? 0;
            $page = $data['page'] ?? 'pages/index/index';
            $code = $data['code'];

            $service = new OfficialService();
            $result = $service->login($code);
            // 获取微信用户详细信息
            $wxUserInfo = $service->getWechatUserInfo($result['access_token'], $result['openid']);

            $userInfo = null;
            if (isset($result['unionid']) && $result['unionid']) {
                $userInfo = User::where('unionid', $result['unionid'])->find();
            }
            if (!$userInfo) {
                $userInfo = User::where('official_openid', $result['openid'])->find();
            }
            if (!$userInfo) {
                $userInfo = $this->addUser(
                    $result['openid'],
                    $result['unionid'] ?? '',
                    $pid,
                    'official',
                    $wxUserInfo->nickname ?? '',
                    $wxUserInfo->headimgurl ?? ''
                );
            }
            $token = $this->getToken($userInfo);
            $webUrl = ConfigProviders::get('site', 'webUrl');
            $url = "{$webUrl}/h5/#/{$page}?token={$token}";
            return redirect($url);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    private function addUser($openid, $unionid, $pid = 0, $source = 'applet', $nickname = '', $avatar = '')
    {
        $user = new User();
        $user->openid = $source == 'applet' ? $openid : '';
        $user->official_openid = $source == 'official' ? $openid : '';
        $user->unionid = $unionid;
        $user->pid = $pid;
        $user->nickname = $nickname ?? '用户' . time();
        $user->avatar = $avatar;
        $user->points = 0;
        $user->source = $source;
        $user->save();
        return $user;
    }

    private function getToken($user)
    {
        $data = [
            'nickname' => $user->nickname,
            'id' => $user->id,
            'access_exp' => 86400 * 7,
            'extend' => 'API'
        ];
        $token = JwtToken::generateToken($data);

        return $token['access_token'];
    }



    /**
     * 获取用户信息
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-26
     */
    public function getUser(Request $request)
    {
        $user = User::where('id', $request->uid)->find();
        if (!$user) {
            return $this->error('用户不存在');
        }
        $userData = [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'points' => $user->points,
            'mobile' => $user->mobile,
            'balance' => $user->balance,
        ];
        return $this->successData($userData);
    }

    /**
     * 获取用户数据统计
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-27
     */
    public function getUserStatistics(Request $request)
    {
        $scene = Scene::where('uid', $request->uid)->where('status', 2)->count();
        $voice = Voice::where('uid', $request->uid)->where('status', 2)->count();
        $works = Works::where('uid', $request->uid)->where('status', 2)->count();
        return $this->successData(compact('scene', 'voice', 'works'));
    }


    /**
     * 修改用户信息
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function editUser(Request $request)
    {
        $data = $request->post();
        $user = User::find($request->uid);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if (isset($data['nickname'])) {
            $user->nickname = $data['nickname'];
        }
        if (isset($data['avatar']) && $data['avatar']) {
            $user->avatar = $data['avatar'];
        }
        if ($user->save()) {
            return $this->success('修改成功');
        }
        return $this->error('修改失败');
    }
}
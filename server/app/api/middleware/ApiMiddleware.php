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

namespace app\api\middleware;

use app\common\utils\JsonUtil;
use Tinywan\Jwt\Exception\JwtTokenException;
use Tinywan\Jwt\Exception\JwtTokenExpiredException;
use Tinywan\Jwt\JwtToken;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class ApiMiddleware implements MiddlewareInterface
{
    use JsonUtil;
    /**
     * 接口中间件
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-20
     */
    public function process(Request $request, callable $handler): Response
    {
        try {
            $request->isWehcatBrowser = strpos(strtolower($request->header('user-agent')), 'micromessenger') !== false;
            $this->handle($request);
        } catch (JwtTokenException $e) {
            return $this->error('token不合法', 401);
        } catch (JwtTokenExpiredException $e) {
            return $this->error('token已过期', 401);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        $response = $handler($request);
        return $response;
    }


    public function handle(Request $request)
    {
        $request->source = 'admin';
        $control = $request->controller;
        //控制器中的方法
        $action = $request->action;
        //获取控制器的默认属性
        $class = new \ReflectionClass($control);
        $properties = $class->getDefaultProperties();
        //不需要登录的方法
        $noLogin = $properties['noLogin'] ?? [];
        $authHeader = $request->header('Authorization');
        $request->loginStatus = $authHeader ? true : false;
        if (in_array($action, $noLogin)) {
            if ($authHeader) {
                try {
                    $request->uid = JwtToken::getCurrentId();
                } catch (JwtTokenException $e) {
                }
            }
            return true;
        }
        if (!$authHeader) {
            throw new \Exception('请先登录', 401);
        }

        $request->uid = JwtToken::getCurrentId();
        return true;
    }
}
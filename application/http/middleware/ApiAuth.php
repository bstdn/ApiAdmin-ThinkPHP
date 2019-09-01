<?php

namespace app\http\middleware;

use app\model\AdminApp;
use app\model\AdminList;
use app\util\ReturnCode;
use think\facade\Cache;

class ApiAuth {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.cross_domain');
        $apiHash = substr($request->path(), 4);
        if($apiHash) {
            $cached = Cache::has('ApiInfo:' . $apiHash);
            if($cached) {
                $apiInfo = Cache::get('ApiInfo:' . $apiHash);
            } else {
                $apiInfo = AdminList::get(['hash' => $apiHash]);
                if($apiInfo) {
                    $apiInfo = $apiInfo->toArray();
                    Cache::set('ApiInfo:' . $apiHash, $apiInfo);
                } else {
                    return json([
                        'code' => ReturnCode::DB_READ_ERROR,
                        'msg'  => '获取接口配置数据失败',
                        'data' => [],
                    ])->header($header);
                }
            }
            $accessToken = $request->header('access-token', '');
            if(!$accessToken) {
                return json([
                    'code' => ReturnCode::AUTH_ERROR,
                    'msg'  => '缺少必要参数access-token',
                    'data' => [],
                ])->header($header);
            }
            if($apiInfo['access_token']) {
                $appInfo = $this->doCheck($accessToken);
            } else {
                $appInfo = $this->doEasyCheck($accessToken);
            }
            if($appInfo === false) {
                return json([
                    'code' => ReturnCode::ACCESS_TOKEN_TIMEOUT,
                    'msg'  => 'access-token已过期',
                    'data' => [],
                ])->header($header);
            }
            $request->APP_CONF_DETAIL = $appInfo;
            $request->API_CONF_DETAIL = $apiInfo;

            return $next($request);
        } else {
            return json([
                'code' => ReturnCode::AUTH_ERROR,
                'msg'  => '缺少接口Hash',
                'data' => [],
            ])->header($header);
        }
    }

    private function doEasyCheck($accessToken) {
        $appInfo = cache('AccessToken:' . $accessToken);
        if(!$appInfo) {
            $appInfo = AdminApp::get(['app_secret' => $accessToken]);
            if(!$appInfo) {
                return false;
            } else {
                $appInfo = $appInfo->toArray();
                cache('AccessToken:' . $accessToken, $appInfo);
            }
        }

        return $appInfo;
    }

    private function doCheck($accessToken) {
        $appInfo = cache('AccessToken:' . $accessToken);
        if(!$appInfo) {
            return false;
        } else {
            return $appInfo;
        }
    }
}

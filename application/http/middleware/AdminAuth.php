<?php

namespace app\http\middleware;

use app\util\ReturnCode;

class AdminAuth {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.cross_domain');
        $ApiAuth = $request->header('apiAuth', '');
        if($ApiAuth) {
            $userInfo = cache('Login:' . $ApiAuth);
            $userInfo = json_decode($userInfo, true);
            if(!$userInfo || !isset($userInfo['id'])) {
                return json([
                    'code' => ReturnCode::AUTH_ERROR,
                    'msg'  => 'ApiAuth不匹配',
                    'data' => [],
                ])->header($header);
            } else {
                $request->API_ADMIN_USER_INFO = $userInfo;
            }

            return $next($request);
        } else {
            return json([
                'code' => ReturnCode::AUTH_ERROR,
                'msg'  => '缺少ApiAuth',
                'data' => [],
            ])->header($header);
        }
    }
}

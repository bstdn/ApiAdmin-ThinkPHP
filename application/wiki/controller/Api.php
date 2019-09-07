<?php

namespace app\wiki\controller;

use app\model\AdminApp;
use app\util\Enum;
use app\util\ReturnCode;

class Api extends Base {

    public function login() {
        $appId = $this->request->post('username');
        $appSecret = $this->request->post('password');
        if(!$appId) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少ApiId');
        }
        if(!$appSecret) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少AppSecret');
        }
        $appInfo = AdminApp::get(['app_id' => $appId, 'app_secret' => $appSecret]);
        if(empty($appInfo)) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, 'AppId或AppSecret错误');
        } elseif($appInfo->app_status == Enum::isFalse) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '当前应用已被封禁，请联系管理员');
        }
        $appInfo = $appInfo->toArray();
        $apiAuth = md5(uniqid() . time());
        cache('WikiLogin:' . $apiAuth, $appInfo, config('apiadmin.online_time'));
        $appInfo['apiAuth'] = $apiAuth;

        return $this->buildSuccess($appInfo, '登录成功');
    }

    public function logout() {
        $ApiAuth = $this->request->header('ApiAuth');
        cache('WikiLogin:' . $ApiAuth, null);
        $oldAdmin = cache('Login:' . $ApiAuth);
        if($oldAdmin) {
            cache('Login:' . $ApiAuth, null);
        }

        return $this->buildSuccess([], '登出成功');
    }
}

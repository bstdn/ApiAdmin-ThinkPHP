<?php

namespace app\admin\controller;

use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\model\AdminMenu;
use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\Enum;
use app\util\ReturnCode;
use app\util\Tools;

class Login extends Base {

    public function index() {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        if(!$username) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少用户名');
        }
        if(!$password) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少密码');
        }
        $userInfo = AdminUser::get(['username' => $username]);
        if(!$userInfo) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名或密码有误');
        } elseif($userInfo['password'] != Tools::encryptPassword($password, $userInfo['salt'])) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名或密码有误');
        } elseif($userInfo['status'] == Enum::isTrue) {
            $userData = $userInfo->userData;
            if($userData) {
                $userData->login_times++;
                $userData->last_login_ip = $this->request->ip(1);
                $userData->last_login_time = time();
                $userData->save();
            } else {
                $data['login_times'] = 1;
                $data['last_login_ip'] = $this->request->ip(1);
                $data['last_login_time'] = time();
                $data['uid'] = $userInfo['id'];
                $data['head_img'] = '';
                AdminUserData::create($data);
                $userInfo['userData'] = $data;
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被禁用，请联系管理员');
        }
        $userInfo['access'] = $this->getAccess($userInfo['id']);
        unset($userInfo['password'], $userInfo['salt']);
        $apiAuth = md5(uniqid() . time());
        if($oldAdmin = cache('Login:' . $userInfo['id'])) {
            cache('Login:' . $oldAdmin, null);
        }
        cache('Login:' . $apiAuth, json_encode($userInfo), config('apiadmin.online_time'));
        cache('Login:' . $userInfo['id'], $apiAuth, config('apiadmin.online_time'));
        $userInfo['apiAuth'] = $apiAuth;

        return $this->buildSuccess($userInfo, '登录成功');
    }

    public function getUserInfo() {
        return $this->buildSuccess($this->userInfo);
    }

    public function logout() {
        $ApiAuth = $this->request->header('ApiAuth');
        cache('Login:' . $ApiAuth, null);
        cache('Login:' . $this->userInfo['id'], null);

        return $this->buildSuccess([], '登出成功');
    }

    public function getAccess($uid) {
        $isSupper = Tools::isAdministrator($uid);
        if($isSupper) {
            $access = AdminMenu::all(['hide' => 0])->toArray();

            return array_values(array_filter(array_column($access, 'url')));
        } else {
            $groups = AdminAuthGroupAccess::get(['uid' => $uid]);
            if(isset($groups) && $groups->group_id) {
                $access = (new AdminAuthRule())->whereIn('group_id', $groups->group_id)->select()->toArray();

                return array_values(array_unique(array_column($access, 'url')));
            } else {
                return [];
            }
        }
    }
}

<?php

namespace app\admin\controller;

use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\ReturnCode;
use app\util\Strs;
use app\util\Tools;

class User extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '', 'intval');
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');
        $obj = new AdminUser();
        if(strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->whereLike('username', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->whereLike('nickname', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('create_time', 'DESC')
            ->paginate($limit, false, ['page' => $start])->each(function($item) {
                $item->userData;
            })->toArray();
        $listInfo = $listObj['data'];
        foreach($listInfo as &$value) {
            if($value['userData']) {
                $value['userData']['last_login_ip'] = long2ip($value['userData']['last_login_ip']);
                $value['userData']['last_login_time'] = date('Y-m-d H:i:s', $value['userData']['last_login_time']);
                $value['create_ip'] = long2ip($value['create_ip']);
            }
        }

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminUser::update([
            'id'     => $id,
            'status' => $status,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        $postData['create_ip'] = request()->ip(1);
        $postData['salt'] = Strs::randString(6);
        $postData['password'] = Tools::encryptPassword($postData['password'], $postData['salt']);
        $userInfo = AdminUser::get(['username' => $postData['username']]);
        if($userInfo) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '用户账号已存在');
        }
        $res = AdminUser::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        if($postData['password'] == '') {
            unset($postData['password']);
        } else {
            $postData['salt'] = Strs::randString(6);
            $postData['password'] = Tools::encryptPassword($postData['password'], $postData['salt']);
        }
        $hasUserInfo = AdminUser::get(['id' => $postData['id']]);
        if(!$hasUserInfo) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '账号不存在');
        } elseif($hasUserInfo['username'] != $postData['username']) {
            $userInfo = AdminUser::get(['username' => $postData['username']]);
            if($userInfo) {
                return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '用户账号已存在');
            }
        }
        $res = AdminUser::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function del() {
        $id = $this->request->get('id');
        if(!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $isAdmin = Tools::isAdministrator($id);
        if($isAdmin) {
            return $this->buildFailed(ReturnCode::INVALID, '超级管理员不能被删除');
        }
        AdminUser::destroy($id);

        return $this->buildSuccess();
    }

    public function own() {
        $postData = $this->request->post();
        $headImg = $postData['head_img'];
        $userInfo = AdminUser::get(['id' => $this->userInfo['id']]);
        if(!$userInfo) {
            return $this->buildFailed(ReturnCode::INVALID, '用户不存在');
        }
        if($postData['password'] && $postData['oldPassword']) {
            $oldPassword = Tools::encryptPassword($postData['oldPassword'], $userInfo['salt']);
            unset($postData['oldPassword']);
            if($oldPassword === $userInfo['password']) {
                $postData['password'] = Tools::encryptPassword($postData['password'], $userInfo['salt']);
            } else {
                return $this->buildFailed(ReturnCode::INVALID, '原始密码不正确');
            }
        } else {
            unset($postData['password']);
            unset($postData['oldPassword']);
        }
        $postData['id'] = $this->userInfo['id'];
        unset($postData['head_img']);
        $res = AdminUser::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $userData = AdminUserData::get(['uid' => $postData['id']]);
        $userData->head_img = $headImg;
        $userData->save();

        return $this->buildSuccess();
    }
}

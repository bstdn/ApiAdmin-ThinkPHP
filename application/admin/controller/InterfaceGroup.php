<?php

namespace app\admin\controller;

use app\model\AdminGroup;
use app\model\AdminList;
use app\util\ReturnCode;

class InterfaceGroup extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $obj = new AdminGroup();
        if(strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('name', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('create_time', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminGroup::update([
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
        $res = AdminGroup::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        $res = AdminGroup::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function del() {
        $hash = $this->request->get('hash');
        if(!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        if($hash === 'default') {
            return $this->buildFailed(ReturnCode::INVALID, '系统预留关键数据，禁止删除');
        }
        AdminList::update(['group_hash' => 'default'], ['group_hash' => $hash]);
        AdminGroup::destroy(['hash' => $hash]);

        return $this->buildSuccess();
    }

    public function getAll() {
        $listInfo = (new AdminGroup())->where(['status' => 1])->select();

        return $this->buildSuccess([
            'list' => $listInfo,
        ]);
    }
}

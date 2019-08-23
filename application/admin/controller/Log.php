<?php

namespace app\admin\controller;

use app\model\AdminUserAction;
use app\util\ReturnCode;

class Log extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $type = $this->request->get('type', '');
        $keywords = $this->request->get('keywords', '');
        $obj = new AdminUserAction();
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->whereLike('url', "%{$keywords}%");
                    break;
                case 2:
                    $obj = $obj->whereLike('nickname', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->where('uid', $keywords);
                    break;
            }
        }
        $listObj = $obj->order('add_time DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function del() {
        $id = $this->request->get('id');
        if(!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminUserAction::destroy($id);

        return $this->buildSuccess();
    }
}

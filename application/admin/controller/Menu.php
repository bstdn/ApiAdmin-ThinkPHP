<?php

namespace app\admin\controller;

use app\model\AdminMenu;
use app\util\ReturnCode;
use app\util\Tools;

class Menu extends Base {

    public function index() {
        $menu_list = (new AdminMenu)->where([])->order('sort', 'ASC')->select()->toArray();
        $menu_list = Tools::formatTree(Tools::listToTree($menu_list));

        return $this->buildSuccess([
            'list' => $menu_list,
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminMenu::update([
            'id'   => $id,
            'hide' => $status,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        if($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        if($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $res = AdminMenu::update($postData);
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
        $childNum = AdminMenu::where(['fid' => $id])->count();
        if($childNum) {
            return $this->buildFailed(ReturnCode::INVALID, '当前菜单存在子菜单,不可以被删除!');
        }
        AdminMenu::destroy($id);

        return $this->buildSuccess();
    }
}

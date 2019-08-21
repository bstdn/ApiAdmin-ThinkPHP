<?php

namespace app\admin\controller;

use app\util\ReturnCode;
use think\Controller;

class Base extends Controller {

    private $debug = [];
    protected $userInfo;

    public function __construct() {
        parent::__construct();
        $this->userInfo = $this->request->API_ADMIN_USER_INFO;
    }

    public function buildSuccess($data = [], $msg = '操作成功', $code = ReturnCode::SUCCESS) {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        if(config('app.app_debug') && $this->debug) {
            $result['debug'] = $this->debug;
        }

        return $result;
    }

    public function buildFailed($code, $msg = '操作失败', $data = []) {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        if(config('app.app_debug') && $this->debug) {
            $result['debug'] = $this->debug;
        }

        return $result;
    }

    protected function debug($data) {
        if($data) {
            $this->debug[] = $data;
        }
    }
}

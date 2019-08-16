<?php

namespace app\api\controller;

use think\facade\App;

class Miss extends Base {

    public function index() {
        $this->debug([
            'TpVersion' => App::version(),
        ]);

        return $this->buildSuccess([
            'Product' => config('apiadmin.app_name'),
            'Version' => config('apiadmin.app_version'),
            'ToYou'   => "Welcome.",
        ]);
    }
}

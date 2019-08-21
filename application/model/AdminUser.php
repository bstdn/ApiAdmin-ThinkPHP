<?php

namespace app\model;

class AdminUser extends Base {

    protected $autoWriteTimestamp = true;

    public function userData() {
        return $this->hasOne('AdminUserData', 'uid', 'id');
    }
}

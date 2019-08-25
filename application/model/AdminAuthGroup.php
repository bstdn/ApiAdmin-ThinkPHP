<?php

namespace app\model;

class AdminAuthGroup extends Base {

    public function rules() {
        return $this->hasMany('AdminAuthRule', 'group_id', 'id');
    }
}

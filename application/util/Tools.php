<?php

namespace app\util;

class Tools {

    public static function encryptPassword($password, $salt) {
        return md5(md5($password).$salt);
    }

    public static function isAdministrator($uid = '') {
        if(!empty($uid)) {
            $adminConf = config('apiadmin.user_administrator');
            if(is_array($adminConf)) {
                if(is_array($uid)) {
                    $m = array_intersect($adminConf, $uid);
                    if(count($m)) {
                        return true;
                    }
                } else {
                    if(in_array($uid, $adminConf)) {
                        return true;
                    }
                }
            } else {
                if(is_array($uid)) {
                    if(in_array($adminConf, $uid)) {
                        return true;
                    }
                } else {
                    if($uid == $adminConf) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

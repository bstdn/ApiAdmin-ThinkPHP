<?php

namespace app\util;

class Tools {

    public static function encryptPassword($password, $salt) {
        return md5(md5($password) . $salt);
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

    public static function buildArrFromObj($res, $key = '') {
        $arr = [];
        foreach($res as $value) {
            $value = $value->toArray();
            if($key) {
                $arr[$value[$key]] = $value;
            } else {
                $arr[] = $value;
            }
        }

        return $arr;
    }

    public static function buildArrByNewKey($array, $keyName = 'id') {
        $list = [];
        foreach($array as $item) {
            $list[$item[$keyName]] = $item;
        }

        return $list;
    }

    public static function listToTree($list, $pk = 'id', $pid = 'fid', $child = 'children', $root = '0') {
        $tree = [];
        if(is_array($list)) {
            $refer = [];
            foreach($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach($list as $key => $data) {
                $parentId = $data[$pid];
                if($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if(isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }

    public static function formatTree($list, $lv = 0, $title = 'name') {
        $formatTree = [];
        foreach($list as $key => $val) {
            $title_prefix = '';
            for($i = 0; $i < $lv; $i++) {
                $title_prefix .= "|---";
            }
            $val['lv'] = $lv;
            $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
            $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix . $val[$title];
            if(!array_key_exists('children', $val)) {
                array_push($formatTree, $val);
            } else {
                $child = $val['children'];
                unset($val['children']);
                array_push($formatTree, $val);
                $middle = self::formatTree($child, $lv + 1, $title);
                $formatTree = array_merge($formatTree, $middle);
            }
        }

        return $formatTree;
    }
}

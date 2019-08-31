<?php

namespace app\util;

class ReturnCode {

    const SUCCESS         = 0;
    const INVALID         = -1;
    const DB_SAVE_ERROR   = -2;
    const FILE_SAVE_ERROR = -6;
    const LOGIN_ERROR     = -7;
    const EMPTY_PARAMS    = -12;
    const AUTH_ERROR      = -14;
    const PARAM_INVALID   = -995;
    const EXCEPTION       = -999;
}

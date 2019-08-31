<?php

namespace app\util;

class DataType {

    const TYPE_INTEGER = 1;
    const TYPE_STRING  = 2;
    const TYPE_ARRAY   = 3;
    const TYPE_FLOAT   = 4;
    const TYPE_BOOLEAN = 5;
    const TYPE_FILE    = 6;
    const TYPE_ENUM    = 7;
    const TYPE_MOBILE  = 8;
    const TYPE_OBJECT  = 9;

    const ALL_TYPE = [
        self::TYPE_INTEGER => 'Integer',
        self::TYPE_STRING  => 'String',
        self::TYPE_BOOLEAN => 'Boolean',
        self::TYPE_ENUM    => 'Enum',
        self::TYPE_FLOAT   => 'Float',
        self::TYPE_FILE    => 'File',
        self::TYPE_MOBILE  => 'Mobile',
        self::TYPE_OBJECT  => 'Object',
        self::TYPE_ARRAY   => 'Array',
    ];
}

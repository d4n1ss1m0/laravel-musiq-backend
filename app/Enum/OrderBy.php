<?php

namespace App\Enum;

enum OrderBy: string
{
    case RECENT = 'recent';
    case ALPAHABET = 'alphabet';
    case CREATED_AT = 'created_at';
//    case AUTHOR = 'author';
}

<?php

namespace App\Enum;

enum AttributeType: string
{
    case STRING = 'string';
    case TEXT = 'text';
    //case IMAGE = 'image';
    case NUMBER = 'number';
    case DATE = 'date';
    case PERIOD = 'period';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';

    public function label(): string
    {
        return match($this) {
            self::STRING => 'String',
            self::TEXT => 'Text',
            //self::IMAGE => 'Picture',
            self::NUMBER => 'Number',
            self::DATE => 'Date',
            self::PERIOD => 'Period',
            self::BOOLEAN => 'Boolean',
            self::SELECT => 'Select',
        };
    }
}

<?php
namespace Helpers;
enum ValueType: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case DATE = 'date'; // YYYY-MM-DD string
}
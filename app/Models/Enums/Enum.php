<?php 

namespace App\Models\Enums;

abstract class Enum
{
    public static function listConstants()
    {
        $class = new \ReflectionClass(get_called_class());
        return $class->getConstants();
    }
}
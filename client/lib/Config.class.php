<?php

class Config
{
    static $values = array();

    static function get($key, $default_value = null)
    {
        if (!isset(self::$values[$key]))
        {
            if (func_num_args() < 2)
            {
                throw new Exception('Configuration key: ' . $key . ' is not set!');
            }

            return $default_value;
        }

        return self::$values[$key];
    }

    static function set($key, $value)
    {
        self::$values[$key] = $value;
    }

    static function setValues($values)
    {
        foreach ($values as $key => $value)
        {
            self::$values[$key] = $value;
        }
    }
}

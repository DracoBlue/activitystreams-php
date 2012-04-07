<?php

class Services
{

    static $instances = array();

    static function get($name)
    {
        if (!isset(self::$instances[$name]))
        {
            $service_class_name = $name . 'Service';
            self::$instances[$name] = new $service_class_name;
        }
        return self::$instances[$name];
    }

    public static function autoload($className)
    {
        $file = dirname(__FILE__) . '/' . $className . '.class.php';
        if (file_exists($file))
        {
            require_once($file);
        }
        $file = dirname(__FILE__) . '/' . $className . '.interface.php';
        if (file_exists($file))
        {
            require_once($file);
        }
    }
}

spl_autoload_register(array('Services', 'autoload'));

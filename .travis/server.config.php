<?php
Config::setValues(
    array(
        'database.dsn' => 'mysql:dbname=naith;host=127.0.0.1;charset=UTF-8',
        "database.user" => "root",
        "database.password" => "",
        "endpoint_base_url" => "http://localhost:8080" . dirname(__FILE__) . "/pub/index.php/"
    )
);

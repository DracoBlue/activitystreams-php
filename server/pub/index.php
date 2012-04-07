<?php
require_once(dirname(dirname(__FILE__)) . '/lib/Services.class.php');
require_once(dirname(dirname(__FILE__)) . '/global.config.php');
require_once(dirname(dirname(__FILE__)) . '/local.config.php');

$dispatcher_service = Services::get('HttpResourceDispatcher');
$response = $dispatcher_service->dispatchRequest($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO'], $_REQUEST);

header('Content-Type: application/json');
echo $response;



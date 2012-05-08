<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

/*
 * To prevent 200 OK status, if an error happened!
 * @see https://bugs.php.net/bug.php?id=50921
 */
header("HTTP/1.0 500 Internal Server Error");

require_once(dirname(dirname(__FILE__)) . '/lib/Services.class.php');
require_once(dirname(dirname(__FILE__)) . '/global.config.php');
require_once(dirname(dirname(__FILE__)) . '/local.config.php');

if ($_SERVER['REQUEST_METHOD'] === 'PUT')
{
    $put_data = file_get_contents('php://input');
    parse_str($put_data, $_REQUEST);
}

$dispatcher_service = Services::get('HttpResourceDispatcher');
$response = $dispatcher_service->dispatchRequest($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO'], $_REQUEST);

/*
 * We have a 200 OK, so reset it no.
 */
header("HTTP/1.0 200 OK");
header('Content-Type: application/json');
echo $response;



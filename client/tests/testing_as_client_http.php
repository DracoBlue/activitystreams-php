<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));

try
{
    $client->get($application->getLink('streams') . 'DOESNOTEXIST?key=value', array('key2' => 'value2'));
}
catch (Exception $exception)
{
    
}

$client->deleteApplication($application);

try
{
    $client->get(dirname($client->getEndpointUrl()) . '/DOES_NOT_EXIST.html');
}
catch (Exception $exception)
{
    assert(strpos($exception->getMessage(), 'failed with status code') > -1);
}

try
{
    $client->get('http://google.com/');
}
catch (Exception $exception)
{
    assert(strpos($exception->getMessage(), 'Invalid json response') > -1);
}

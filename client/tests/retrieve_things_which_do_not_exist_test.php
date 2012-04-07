<?php

$client = new AsClient(Config::get('endpoint_base_url'));

try
{
    $client->getObjectById('doesnotexist');
    assert(false);
}
catch (Exception $exception)
{

}

try
{
    $client->getStreamById('doesnotexist');
    assert(false);
}
catch (Exception $exception)
{

}

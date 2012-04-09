<?php

/*
 * Create a new app for this test!
 */
$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));

try
{
    $application->getLink('doesnotexist');
    assert(false);
}
catch (Exception $exception)
{

}

try
{
    $application->getStreamById('doesnotexist');
    assert(false);
}
catch (Exception $exception)
{

}
try
{
    $application->getObjectById('doesnotexist');
    assert(false);
}
catch (Exception $exception)
{

}

$client->deleteApplication($application);

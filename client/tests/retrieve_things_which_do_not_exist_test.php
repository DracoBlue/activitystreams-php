<?php

/*
 * Create a new app for this test!
 */
$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));

try
{
    $client->getApplicationByIdAndSecret($application->getId(), $application->getSecret() . '___');
    assert(false);
}
catch (Exception $exception)
{

}

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


try
{
    $client->getApplicationByIdAndSecret('doesnotexist', 'is_wrong');
    assert(false);
}
catch (Exception $exception)
{

}

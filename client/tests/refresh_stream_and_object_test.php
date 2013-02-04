<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$guest_stream = $application->recreateStream('guest', array('name' => 'guest stream', 'auto_subscribe' => 1));

assert($guest_stream->getName() == 'guest stream');
assert($guest_stream->isAutoSubscribe());

/*
 * Set name + autosubscribe
 */
 
$guest_stream = $application->recreateStream('guest', array('name' => 'guest stream with new name', 'auto_subscribe' => 0));

assert($guest_stream->getName() == 'guest stream with new name');
assert($guest_stream->isAutoSubscribe() == false);

/*
 * Reset to default values (autosubscribe = true, name = '')
 */
 
$guest_stream = $application->recreateStream('guest', array());

assert($guest_stream->getName() == '');
assert($guest_stream->isAutoSubscribe());

$application->deleteStream($guest_stream);
$client->deleteApplication($application);

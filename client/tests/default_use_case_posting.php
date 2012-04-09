<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$private_stream = $application->createStream('private_stream', array('auto_subscribe' => 0));
$guest = $application->createObject('guest');

$media_comments_stream->createActivity(array('title' => 'I posted a link!', 'verb' => 'post'));
$private_stream->createActivity(array('title' => 'I posted a link2!', 'verb' => 'post'));

$activites = $guest->getFeed();
assert(count($activites) == 1);

$guest->subscribeToStream($private_stream);

$activites = $guest->getFeed();
assert(count($activites) == 2);

$guest->unsubscribeFromStream($private_stream);

$activites = $guest->getFeed();
assert(count($activites) == 1);

$application->deleteStream($private_stream);
$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);

// $application = $client->getApplicationById('my_app');
// $media_comments_stream = $application->getStreamById('media_comments');
// $guest = $application->getObjectById('guest');

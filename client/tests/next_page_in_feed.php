<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$private_stream = $application->createStream('private_stream', array('auto_subscribe' => 0));
$guest = $application->createObject('guest');

$media_comments_stream->createActivity(array('title' => 'I posted a link!', 'verb' => 'post'), $guest);
$private_stream->createActivity(array('title' => 'I posted a link2!', 'verb' => 'post'), $guest, $guest, $guest);
$private_stream->createActivity(array('title' => 'I posted a link3!', 'verb' => 'post'), $guest, $guest, $guest);

$guest->subscribeToStream($private_stream);

$activities = $guest->getFeed(0, 3);
assert(count($activities) == 3);
$first_activity = $activities[0];
$second_activity = $activities[1];
$third_activity = $activities[2];

$activities = $guest->getFeed(0, 1);
assert(count($activities) == 1);
assert($first_activity->getId() == $activities[0]->getId());

$activities = $guest->getFeed(0, 1, $first_activity);
assert(count($activities) == 1);
assert($second_activity->getId() == $activities[0]->getId());

$activities = $guest->getFeed(0, 2, $first_activity);
assert(count($activities) == 2);
assert($second_activity->getId() == $activities[0]->getId());
assert($third_activity->getId() == $activities[1]->getId());

$activities = $guest->getFeed(0, 1, $second_activity);
assert(count($activities) == 1);
assert($third_activity->getId() == $activities[0]->getId());

$application->deleteStream($private_stream);
$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);
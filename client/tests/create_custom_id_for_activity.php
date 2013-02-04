<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$guest = $application->createObject('guest');

$custom_activity_id = 'mysupercustomid';
$activity = $media_comments_stream->createActivity(array('id' => $custom_activity_id,  'title' => 'I posted a link!', 'verb' => 'post'));

$activites = $guest->getFeed();
assert(count($activites) == 1);

$activity = $activites[0];

assert($activity->getId() ==  $custom_activity_id);

$application->deleteActivity($activity);
    
$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);
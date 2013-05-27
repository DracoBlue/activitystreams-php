<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$guest = $application->createObject('guest');

$activity = $media_comments_stream->createActivity(array('title' => 'I posted a link!', 'verb' => 'post'));

$activites = $guest->getFeed();
assert(count($activites) == 1);

$activity = $activites[0];

$custom_activity_id = $activity->getId();

$found_activity = $application->getActivityById($custom_activity_id);

assert($found_activity->getId() == $activity->getId());

$application->deleteActivity($found_activity);

try
{
    $not_found_activity = $application->getActivityById($custom_activity_id);
    assert(false);
}
catch (Exception $exception)
{

}

$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);
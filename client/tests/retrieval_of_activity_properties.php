<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$guest = $application->createObject('guest', array('foo' => 'bar'));
$post_object = $application->createObject('post', array('foo' => 'bar'));
$target_object = $application->createObject('target', array('foo' => 'bar'));

$media_comments_stream->createActivity(array('title' => 'I posted a link!', 'verb' => 'post'));

$activites = $guest->getFeed();
assert(count($activites) == 1);

$activity = $activites[0];
print_r($activity);

assert($activity->getActorId() == null);
assert($activity->getTargetId() == null);
assert($activity->getObjectId() == null);

try
{
    $activity->getActor();
    assert(false);
} catch (Exception $exception)
{
    /*
     * Yes, we don't have one!
     */
}

try
{
    $activity->getTarget();
    assert(false);
} catch (Exception $exception)
{
    /*
     * Yes, we don't have one!
     */
}

try
{
    $activity->getObject();
    assert(false);
} catch (Exception $exception)
{
    /*
     * Yes, we don't have one!
     */
}

$application->deleteActivity($activity);

$media_comments_stream->createActivity(array('title' => 'I posted a link!', 'verb' => 'post'), $guest, $post_object, $target_object);

$activites = $guest->getFeed();
assert(count($activites) == 1);

$activity = $activites[0];
print_r($activity);

assert($activity->getActorId() == $guest->getId());
assert($activity->getTargetId() == $target_object->getId());
assert($activity->getObjectId() == $post_object->getId());

assert($activity->getActor()->getId() == $guest->getId());
assert($activity->getTarget()->getId() == $target_object->getId());
assert($activity->getObject()->getId() == $post_object->getId());

    
$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);
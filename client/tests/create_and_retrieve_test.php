<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$media_comments_stream = $application->createStream('media_comments');
$guest = $application->createObject('guest');

$application_retrieved = $client->getApplicationByIdAndSecret($application->getId(), $application->getSecret());
assert($application_retrieved->getId() == $application->getId());

$media_comments_stream_retrieved = $application->getStreamById($media_comments_stream->getId());
assert($media_comments_stream_retrieved->getId() == $media_comments_stream->getId());

$guest_retrieved = $application->getObjectById($guest->getId());
assert($guest_retrieved->getId() == $guest->getId());

$application->deleteStream($media_comments_stream);
$application->deleteObject($guest);
$client->deleteApplication($application);
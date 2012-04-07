<?php

/*
 * FIXME: remove this part, it's just to reset the database for testing purpose
 */
Services::get('JsonHttpClient')->delete(Config::get('endpoint_base_url') . 'api/default');

$client = new ActivityStreamClient(Config::get('endpoint_base_url'));

$public_stream = $client->createStream('Public TestStream', true);
$public_stream = $client->getStreamById($public_stream['id']);
$private_stream = $client->createStream('Private TestStream', false);
$actor1 = $client->createObject(array('displayName' => 'User1', 'fb_id' => '!23', 'objectType' => 'person'));
$actor1 = $client->getObjectById($actor1['id']);
$actor2 = $client->createObject(array('displayName' => 'User2'));
print_r($public_stream);
print_r($actor1);

$blog = $client->createObject(array('object_type' => 'blog', 'url' => 'http://dracoblue.net'));
$activity_data = $client->createActivity($public_stream, $actor1, array('verb' => 'post', 'title' => 'I posted a (public) new link'), $blog);

sleep(1.1);

$second_blog = $client->createObject(array('url' => 'http://webdevberlin.com'));
$activity_data = $client->createActivity($private_stream, $actor1, array('verb' => 'post', 'title' => 'I posted a (private) new link'), $second_blog);

/*
 * Get the feed (should only include the public stream post)
 */
$activities = $client->getFeedForObject($actor1);
print_r($activities);

assert(count($activities['items']) === 1);

/*
 * subscribe to the private stream
 */

$client->subscribeActorToStream($actor1, $private_stream);

$activities = $client->getFeedForObject($actor1);
print_r($activities);

assert(count($activities['items']) === 2);

/*
 * limit + offset
 */
$activities = $client->getFeedForObject($actor1, 0, 1);
print_r($activities);

assert(count($activities['items']) === 1);

/*
 * Unsubscribe and see if we have just 1 element now
 */
$client->unsubscribeActorFromStream($actor1, $private_stream);

$activities = $client->getFeedForObject($actor1);
print_r($activities);
assert(count($activities['items']) === 1);

$client->deleteStream($public_stream);
$client->deleteStream($private_stream);
$client->deleteObject($actor1);
$client->deleteObject($actor2);

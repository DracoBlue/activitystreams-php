<?php

/*
 * FIXME: remove this part, it's just to reset the database for testing purpose
 */
Services::get('JsonHttpClient')->delete(Config::get('endpoint_base_url') . 'api/default');

$client = new AsClient(Config::get('endpoint_base_url'));
$actor1 = $client->createObject(array('displayName' => 'User1', 'foo_attribute' => '!23', 'objectType' => 'person'));

$public_stream = $client->createStream('Public TestStream', true);
$private_stream = $client->createStream('Private TestStream', false);

print_r($public_stream);
print_r($actor1);

$blog = $client->createObject(array('object_type' => 'blog', 'url' => 'http://dracoblue.net'));
$activity_data = $client->createActivity($public_stream, array('verb' => 'post', 'title' => 'I posted a (public) new link'), $actor1, $blog);

sleep(1.1);

$second_blog = $client->createObject(array('url' => 'http://webdevberlin.com'));
$activity_data = $client->createActivity($private_stream, array('verb' => 'post', 'title' => 'I posted a (private) new link', 'bar_attribute' => 1337), $actor1, $second_blog);

/*
 * Get the feed (should only include the public stream post)
 */
$activities = $client->getFeedForObject($actor1);
print_r($activities);

assert(count($activities['items']) === 1);

/*
 * subscribe to the private stream
 */

$client->subscribeObjectToStream($actor1, $private_stream);

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
$client->unsubscribeObjectFromStream($actor1, $private_stream);

$activities = $client->getFeedForObject($actor1);
print_r($activities);
assert(count($activities['items']) === 1);

// $client->deleteStream($public_stream);
// $client->deleteStream($private_stream);
// $client->deleteObject($actor1);

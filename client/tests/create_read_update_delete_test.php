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
$second_blog_post = $client->createObject(array('url' => 'http://webdevberlin.com/post/1'));
$activity_data = $client->createActivity($private_stream, array('verb' => 'post', 'title' => 'I posted a (private) new link', 'bar_attribute' => 1337), $actor1, $second_blog, $second_blog_post);

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

/*
 * Be sure that getters work
 */
$public_stream = $client->getStreamById($public_stream->getId());
assert($public_stream->getName() == 'Public TestStream');
$blog = $client->getObjectById($blog->getId());
assert($blog->getUrl() == 'http://dracoblue.net');
$actor1_values = $actor1->getValues();
assert($actor1_values['displayName'] === 'User1');
/*
 * Try to get a nonexisting link
 */
try
{
    $blog->getLink('DOESNOTEXIST');
    assert(false);
}
catch (Exception $exception)
{
    
}

$client->deleteStream($public_stream);
$private_stream->delete();
$client->deleteObject($actor1);
$blog->delete();
$client->deleteObject($second_blog);

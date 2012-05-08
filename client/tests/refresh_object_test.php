<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$guest = $application->recreateObject('guest', array('url' => ''));

assert($guest->getUrl() === null);
assert($guest->getObjectType() === null);
assert(count($guest->getValues()) == 0);

$guest = $application->recreateObject('guest', array('foo' => 'bar'));

assert($guest->getUrl() === null);
assert($guest->getObjectType() === null);
assert(count($guest->getValues()) == 1);

$values = $guest->getValues();

assert($values['foo'] === 'bar');

$guest = $application->recreateObject('guest', array('foo' => 'bar2', 'url' => 'http://example.org'));
$values = $guest->getValues();

assert($guest->getUrl() === 'http://example.org');
assert($guest->getObjectType() === null);
assert($values['foo'] === 'bar2');
assert(count($guest->getValues()) == 1);

$guest = $application->recreateObject('guest', array('foo2' => 'bar21', 'objectType' => 'person'));
$values = $guest->getValues();

assert($values['foo2'] === 'bar21');
assert($guest->getObjectType() === 'person');
assert($guest->getUrl() === null);
assert(count($guest->getValues()) == 1);

$application->deleteObject($guest);
$client->deleteApplication($application);

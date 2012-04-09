<?php

$application = $client->createApplication('my_app', array('name' => 'Test Application: ' . basename(__FILE__)));
$guest = $application->createObject('guest', array('url' => ''));

assert($guest->getUrl() === null);
assert($guest->getObjectType() === null);
assert(count($guest->getValues()) == 0);

$guest = $application->updateObject($guest, array('foo' => 'bar'));

assert($guest->getUrl() === null);
assert($guest->getObjectType() === null);
assert(count($guest->getValues()) == 1);

$values = $guest->getValues();

assert($values['foo'] === 'bar');

$guest = $application->updateObject($guest, array('foo' => 'bar2', 'url' => 'http://example.org'));
$values = $guest->getValues();

assert($guest->getUrl() === 'http://example.org');
assert($values['foo'] === 'bar2');

$guest = $application->updateObject($guest, array('foo2' => 'bar21', 'objectType' => 'person'));
$values = $guest->getValues();

assert($values['foo'] === 'bar2');
assert($values['foo2'] === 'bar21');
assert($guest->getObjectType() === 'person');
assert($guest->getUrl() === 'http://example.org');
assert(count($guest->getValues()) == 2);

$application->deleteObject($guest);
$client->deleteApplication($application);
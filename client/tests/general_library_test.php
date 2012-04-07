<?php

try
{
    Config::get('key which is not set!!');
    assert(false);
}
catch (Exception $exception)
{

}

$value = Config::get('key which is not set!!', 123);
assert($value === 123);

Config::set('key which is not set!!', 123);
$value = Config::get('key which is not set!!');
assert($value === 123);

/*
 * Request a non json file (our README.md)
 */
try
{
    Services::get('JsonHttpClient')->get(dirname(dirname(dirname(Config::get('endpoint_base_url')))) . '/README.md');
    assert(false);
}
catch (Exception $exception)
{
    assert(strpos($exception->getMessage(), 'Invalid json') !== false);    
}

/*
 * Request a NON existant file
 */

try
{
    Services::get('JsonHttpClient')->get(dirname(dirname(dirname(Config::get('endpoint_base_url')))) . '/DOESNOTEXIST');
    assert(false);
}
catch (Exception $exception)
{
}

/*
 * Retrieve existing file with appended query strings
 */
Services::get('HttpClient')->get(dirname(dirname(dirname(Config::get('endpoint_base_url')))) . '/README.md?hans=key', array("key2" => "value"));


/*
 * Use http auth
 */
Services::get('HttpClient')->get(dirname(dirname(dirname(Config::get('endpoint_base_url')))) . '/README.md?hans=key', array("key2" => "value"), array("user", "password"));


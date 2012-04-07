<?php

class JsonHttpClientService extends HttpClientService
{
    protected function rawRequest($method, $url, array $options, array $values, array $auth)
    {
        $raw_response = parent::rawRequest($method, $url, $options, $values, $auth);
        
        $response = @json_decode($raw_response, true);
        if ($response === null && $raw_response !== 'null' && $raw_response !== '')
        {
            throw new Exception('Invalid json response: ' . $raw_response);
        }
        return $response;
    }
}

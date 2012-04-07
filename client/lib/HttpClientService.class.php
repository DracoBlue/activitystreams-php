<?php

class HttpClientService
{
    protected function rawRequest($method, $url, array $options, array $values, array $auth)
    {
        $options[CURLOPT_URL] = $url;
        
        $default_options = array();
        $default_options[CURLOPT_CUSTOMREQUEST] = $method;
        $default_options[CURLOPT_RETURNTRANSFER] = 1;
        $default_options[CURLOPT_FOLLOWLOCATION] = 1;
        $default_options[CURLOPT_HEADER] = 0;
        $default_options[CURLOPT_HTTPHEADER] = array("Expect:");
        $default_options[CURLOPT_FRESH_CONNECT] = 1;
        
        if (count($auth))
        {
            $default_options[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
            $default_options[CURLOPT_USERPWD] = implode(':', $auth);
        }
        
        if (count($values))
        {
            $encoded_values = array();
            foreach ($values as $key => $value)
            {
                $encoded_values[] = urlencode($key) . '=' . urlencode($value);
            }

            $url = $options[CURLOPT_URL];

            if (strpos($url, '?') === false)
            {
                $url .= '?';
            }
            else
            {
                $url .= '&';
            }

            $options[CURLOPT_URL] = $url . implode('&', $encoded_values);
        }

        $ch = curl_init();

        foreach ($default_options as $option => $value)
        {
            curl_setopt($ch, $option, $value);
        }
        
        foreach ($options as $option => $value)
        {
            curl_setopt($ch, $option, $value);
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        
        if ($status['http_code'] < 200 || $status['http_code'] > 299)
        {
            throw new Exception('Requesting ' . $options[CURLOPT_URL] . ' failed with status code ' . $status['http_code'] . ' and response: ' . $response);
        }

        return $response;
    }

    public function get($url, array $values = array(), $auth = array())
    {
        return $this->rawRequest('GET', $url, array(), $values, $auth);
    }

    public function post($url, array $values = array(), $auth = array())
    {
        echo $url;
        print_r($values);
        $encoded_values = array();
        foreach ($values as $key => $value)
        {
            $encoded_values[] = urlencode($key) . '=' . urlencode($value);
        }

        return $this->rawRequest('POST', $url, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => implode('&', $encoded_values)
        ), array(), $auth);
    }

    public function patch($url, array $values = array(), $auth = array())
    {
        return $this->rawRequest('PATCH', $url, array(), $values, $auth);
    }

    public function put($url, array $values = array(), $auth = array())
    {
        return $this->rawRequest('PUT', $url, array(), $values, $auth);
    }

    public function delete($url, array $values = array(), $auth = array())
    {
        return $this->rawRequest('DELETE', $url, array(), $values, $auth);
    }

}

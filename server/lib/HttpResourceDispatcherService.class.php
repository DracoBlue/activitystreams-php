<?php
class HttpResourceDispatcherService
{
    public function dispatchRequest($verb, $path, array $parameters)
    {
        if (substr($path, 0, 1) !== '/')
        {
            throw new Exception('Expected a valid path value for this request (e.g. /streams/1234), but the path did\'t start with a \'/\'.');
        }

        $path_parts = explode('/', substr($path, 1));

        $service_name = str_replace(' ', '', ucfirst(ucwords(str_replace('-', ' ', $path_parts[0]))));

        if ($service_name == '')
        {
            $service_name = 'Api';
            $path_parts = array('Api', 'default');
        }

        /*
         * FIXME: Validate service name! Should be only a-z and -
         */

        /**
         * @var HttpResourceService
         */
        $service = Services::get($service_name);

        if (!$service instanceof HttpResourceService)
        {
            throw new Exception('The requested resource does not implement the HttpResourceService interface.');
        }

        $path_parts_without_resource_name = array_slice($path_parts, 1);

        $method_name = null;
        $method_arguments = array();
        $has_multiple_results = false;

        if ($verb === 'GET' && count($path_parts_without_resource_name) === 0)
        {
            /*
             * GET /streams
             */
            $method_name = 'get' . $service->getResourceNamePluralized();
            $method_arguments = array($parameters);
            $has_multiple_results = true;
        }
        if ($verb === 'POST' && count($path_parts_without_resource_name) === 0)
        {
            /*
             * POST /streams
             */
            $method_name = 'post' . $service->getResourceNameSingularized();
            $method_arguments = array($parameters);
        }
        elseif (in_array($verb, array(
            'GET',
            'PUT',
            'DELETE',
            'PATCH'
        )) && count($path_parts_without_resource_name) === 1)
        {
            /*
             * e.g. GET /streams/12321323123121
             */
            $method_name = strtolower($verb) . $service->getResourceNameSingularized();
            $method_arguments = array(
                $path_parts_without_resource_name[0],
                $parameters
            );
        }

        if (!$method_name)
        {
            throw new Exception('Unsupported method (verb: ' . $verb . ', path: ' . $path . ')!');
        }

        $response = call_user_func_array(array(
            $service,
            $method_name
        ), $method_arguments);

        if ($has_multiple_results)
        {
            $converted_results = array();

            foreach ($response as $raw_result)
            {
                $converted_results[] = call_user_func_array(array(
                    $service,
                    'convertResourceToJson'
                ), array($raw_result));
            }

            return '[' . implode(',', $converted_results) . ']';
        }
        else
        {
            if ($response === null)
            {
                return '';
            }
            
            return call_user_func_array(array(
                $service,
                'convertResourceToJson'
            ), array($response));
        }

        return $response;
    }

}

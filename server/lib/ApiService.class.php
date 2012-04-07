<?php

class ApiService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Apis';
    }

    public function getResourceNameSingularized()
    {
        return 'Api';
    }

    public function convertResourceToJson(array $api_entries)
    {
        $links = array();
        foreach ($api_entries as $api_entry)
        {
            $links[] = array(
                'rel' => $api_entry['name'],
                'href' => Config::get('endpoint_base_url') . $api_entry['path']
            );
        }

        return json_encode(array('links' => $links));
    }

    /**
     * @return Stream[]
     */
    public function getApi($api_name, array $values)
    {
        return array(
            array(
                'name' => 'streams',
                'path' => 'stream'
            ),
            array(
                'name' => 'actors',
                'path' => 'actor'
            )
        );
    }
    
    /**
     * FIXME: Remove this function, it's just for testing purpose to reset the database!
     */
    public function deleteApi($api_name, array $values)
    {
        $db_service = Services::get('Database');
        $db_service->deleteTableRows('activities', '1');
        $db_service->deleteTableRows('activity_streams', '1');
        $db_service->deleteTableRows('activity_stream_actors', '1');
        $db_service->deleteTableRows('activity_stream_subscriptions', '1');
        $db_service->deleteTableRows('activity_stream_unsubscriptions', '1');
        
        return array();
    }

}

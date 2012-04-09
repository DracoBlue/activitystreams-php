<?php
class ApplicationService extends HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Applications';
    }

    public function getResourceNameSingularized()
    {
        return 'Application';
    }

    public function convertResourceToJson(array $application)
    {
        $application['links'] = array(
            array(
                'rel' => 'delete',
                'href' => Config::get('endpoint_base_url') . 'application/' . urlencode($application['id'])
            ),
            array(
                'rel' => 'streams',
                'href' => Config::get('endpoint_base_url') . 'stream'
            ),
            array(
                'rel' => 'objects',
                'href' => Config::get('endpoint_base_url') . 'object'
            )
        );
        return json_encode($application);
    }

    /**
     * @return array
     */
    public function getApplication($application_id, array $values = array())
    {
        $db_service = Services::get('Database');
        return $db_service->getTableRow('applications', 'id = ?', array($application_id));
    }

    /**
     * @return array
     */
    public function getApplications(array $values = array())
    {
        if (!isset($values['application_id']))
        {
            throw new Exception('Cannot filter for applications, if no application_id is given!');
        }
        
        if (!isset($values['secret']))
        {
            throw new Exception('Cannot filter for applications, if no application_id is given!');
        }
        
        $application_id = $values['application_id'];
        $secret = $values['secret'];
        
        $db_service = Services::get('Database');
        return $db_service->getTableRows('applications', 'id = ? and secret = ?', array($application_id, $secret));
    }

    public function deleteApplication($application_id, array $values = array())
    {
        $db_service = Services::get('Database');
        $db_service->deleteTableRows('objects', 'application_id = ?', array($application_id));
        $db_service->deleteTableRow('applications', 'id = ?', array($application_id));
    }

    /**
     * @return Activity
     */
    public function postApplication(array $values)
    {
        $db_service = Services::get('Database');
        $object_service = Services::get('Object');
        $stream_service = Services::get('Stream');
        
        if (isset($values['name']))
        {
            $raw_values['name'] = $values['name'];
            unset($values['name']);
        }
        
        $raw_values['id'] = $values['id'];
        unset($values['id']);
        
        $raw_values['secret'] = $db_service->generateUuid(32);
        
        $application_id = $db_service->createTableRow('applications', $raw_values);
        
        return $this->getApplication($application_id);
    }

}

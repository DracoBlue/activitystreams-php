<?php
class ObjectService extends HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Objects';
    }

    public function getResourceNameSingularized()
    {
        return 'Object';
    }

    public function convertResourceToJson(AsObject $object)
    {
        $values = $object->getValues();
        $values['links'] = array(
            array(
                'rel' => 'feed',
                'href' => Config::get('endpoint_base_url') . 'feed/' . urlencode($object->getId())
            ),
            array(
                'rel' => 'subscriptions',
                'href' => Config::get('endpoint_base_url') . 'subscription?object_id=' . urlencode($object->getId()) . '&application_id=' . urlencode($object->getApplicationId())
            ),
            array(
                'rel' => 'delete',
                'href' => Config::get('endpoint_base_url') . 'object/' . urlencode($object->getId())
            )
        );

        return json_encode($values);
    }

    /**
     * @return AsObject[]
     */
    public function getObjects(array $values)
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        
        if (!isset($values['object_id']))
        {
            throw new Exception('Cannot search for actors if no object_id is given!');    
        }
        
        $object_id = $values['object_id'];

        $objects = array();
        foreach ($db_service->getTableRows('objects', 'id = ? AND application_id = ?', array($object_id, $application_id)) as $row)
        {
            $objects[] = new AsObject($row);
        }

        return $objects;
    }

    /**
     * @return AsObject
     */
    public function getObject($object_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('objects', 'id = ? AND application_id = ?', array($object_id, $application_id));
        return new AsObject($row);
    }

    public function deleteObject($object_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');

        $object = $this->getObject($object_id);

        $db_service->deleteTableRows('subscriptions', 'object_id = ? AND application_id = ?', array($object_id, $application_id));
        $db_service->deleteTableRows('unsubscriptions', 'object_id = ? AND application_id = ?', array($object_id, $application_id));

        $db_service->deleteTableRow('objects', 'id = ? AND application_id = ?', array($object_id, $application_id));
    }

    /**
     * @return AsObject
     */
    public function postObject(array $values)
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        $raw_values = array();
        
        $raw_values['application_id'] = $application_id;
        
        $raw_values['id'] = $values['id'];
        unset($values['id']);
        
        if (isset($values['url']))
        {
            $raw_values['url'] = $values['url'];
            unset($values['url']);
        }

        if (isset($values['object_type']))
        {
            $raw_values['object_type'] = $values['object_type'];
            unset($values['object_type']);
        }
        
        /*
         * Everything else can be set by using the values json serialized blob
         */
        if (!empty($values))
        {
            $raw_values['values'] = json_encode($values);
        }

        $object_id = $db_service->createTableRow('objects', $raw_values);

        return $this->getObject($object_id);
    }

}

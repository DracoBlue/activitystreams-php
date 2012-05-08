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
            ),
            array(
                'rel' => 'update',
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

    /**
     * @return AsObject
     */
    public function patchObject($object_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        $object = $this->getObject($object_id);
        
        if (empty($values))
        {
            /*
             * Nothing to do ...
             */
            return $object;
        }
        
        $raw_values = array();

        if (array_key_exists('url', $values))
        {
            $raw_values['url'] = $values['url'];
            unset($values['url']);
        }

        if (array_key_exists('objectType', $values))
        {
            $raw_values['object_type'] = $values['objectType'];
            unset($values['objectType']);
        }
        
        /*
         * Everything else can be set by using the values json serialized blob
         */
        if (count($values) > 0)
        {
            $original_values = $object->getValues();
            /*
             * Get rid of the stuff, which we store right into the object!
             */
            unset($original_values['objectType']);
            unset($original_values['id']);
            unset($original_values['url']);
            foreach ($original_values as $key => $value)
            {
                if (!array_key_exists($key, $values))
                {
                    $values[$key] = $value;
                }
            }
            $raw_values['values'] = json_encode($values);
        }
        
        $update_query = array();
        $where_parameters = array();
        
        foreach ($raw_values as $key => $value)
        {
            $update_query[] = '`' . $key . '` = ?';
            $where_parameters[] = $value;
        }
        
        $where_parameters[] = $object_id;
        $where_parameters[] = $application_id;
        
        $db_service->updateTableRows('objects', implode(', ', $update_query), 'id = ? AND application_id = ?', $where_parameters);
        
        return $this->getObject($object_id);
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
    public function putObject($object_id, array $values)
    {
        $object = $this->getObject($object_id);
        
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        
        if (!array_key_exists('url', $values))
        {
            $values['url'] = null;
        }
        
        if (!array_key_exists('objectType', $values))
        {
            $values['objectType'] = null;
        }
        
        /*
         * It does exist, so reset all values and patch it afterwards
         */
        $db_service->updateTableRows('objects', '`values` = NULL', 'id = ? AND application_id = ?', array($object->getId(), $application_id));
        
        return $this->patchObject($object->getId(), $values);
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

        if (isset($values['objectType']))
        {
            $raw_values['object_type'] = $values['objectType'];
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

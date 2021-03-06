<?php
class ActivityService extends HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Activities';
    }

    public function getResourceNameSingularized()
    {
        return 'Activity';
    }

    public function convertResourceToJson(Activity $activity)
    {
        $values = $activity->getValues();
        $values['links'] = array(
            array(
                'rel' => 'delete',
                'href' => Config::get('endpoint_base_url') . 'activity/' . urlencode($activity->getId())
            )
        );

        return json_encode($values);
    }
    
    /**
     * @return Activity[]
     */
    public function getActivities(array $values)
    {
        $db_service = Services::get('Database');

        if (!isset($values['activity_id']))
        {
            throw new Exception('Cannot search for activites if no activity_id is given!');    
        }
        
        $activity_id = $values['activity_id'];
        $application_id = $this->getAuthenticatedApplicationId();

        $activities = array();
        foreach ($db_service->getTableRows('activities', 'id = ? and application_id = ?', array($activity_id, $application_id)) as $row)
        {
            $activities[] = new Activity($row);
        }

        return $activities;
    }

    /**
     * @return Activity
     */
    public function getActivity($activity_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('activities', 'id = ? AND application_id = ?', array($activity_id, $application_id));
        return new Activity($row);
    }

    /**
     * @return Activity
     */
    public function postActivity(array $values)
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');
        $object_service = Services::get('Object');
        $stream_service = Services::get('Stream');
        
        $raw_values['application_id'] = $application_id;
        unset($values['application_id']);
        
        $stream = $stream_service->getStream($values['stream_id'], array('application_id' => $application_id));
        $raw_values['stream_id'] = $stream->getId();
        unset($values['stream_id']);
        

        $raw_values['title'] = $values['title'];
        unset($values['title']);
        
        $raw_values['verb'] = $values['verb'];
        unset($values['verb']);
        
        if (isset($values['id']))
        {
            $raw_values['id'] = $values['id'];
            unset($values['id']);
        }
        
        if (isset($values['object_id']))
        {
            $raw_values['object_id'] = $object_service->getObject($values['object_id'])->getId();
            unset($values['object_id']);
        }
        
        if (isset($values['actor_id']))
        {
            $raw_values['actor_id'] = $object_service->getObject($values['actor_id'])->getId();
            unset($values['actor_id']);
        }
        
        if (isset($values['target_id']))
        {
            $raw_values['target_id'] = $object_service->getObject($values['target_id'])->getId();
            unset($values['target_id']);
        }

        /*
         * Everything else can be set by using the values json
         * serialized blob
         */
        if (!empty($values))
        {
            $raw_values['values'] = json_encode($values);
        }

        $activity_id = $db_service->createTableRow('activities', $raw_values);
        
        return $this->getActivity($activity_id);
    }
    
    public function deleteActivity($activity_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        $db_service = Services::get('Database');

        $activity = $this->getActivity($activity_id);

        $db_service->deleteTableRow('activities', 'id = ? AND application_id = ?', array($activity_id, $application_id));
    }

}

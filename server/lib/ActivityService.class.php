<?php
class ActivityService implements HttpResourceService
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
        return json_encode($activity->getValues());
    }

    /**
     * @return Activity
     */
    public function getActivity($activity_id, array $values = array())
    {
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('activities', 'id = ?', array($activity_id));
        return new Activity($row);
    }

    /**
     * @return Activity
     */
    public function postActivity(array $values)
    {
        $db_service = Services::get('Database');
        $object_service = Services::get('Object');
        $stream_service = Services::get('Stream');
        
        $stream = $stream_service->getStream($values['stream_id']);
        $raw_values['stream_id'] = $stream->getId();
        unset($values['stream_id']);

        $raw_values['title'] = $values['title'];
        unset($values['title']);
        
        $raw_values['verb'] = $values['verb'];
        unset($values['verb']);
        
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

}

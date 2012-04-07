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
        return json_encode(array(
            'id' => $activity->getId(),
            'title' => $activity->getTitle()
        ));
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
        $actor_service = Services::get('Actor');
        $stream_service = Services::get('Stream');
        
        $actor = $actor_service->getActor($values['actor_id']);
        $stream = $stream_service->getStream($values['stream_id']);

        $raw_values['title'] = $values['title'];
        unset($values['title']);
        
        $raw_values['actor_id'] = $actor->getId();
        unset($values['actor_id']);
        
        $raw_values['stream_id'] = $stream->getId();
        unset($values['stream_id']);

        if (isset($values['object_type']))
        {
            $raw_values['object_type'] = $values['object_type'];
            unset($values['object_type']);
        }
        
        /*
         * Everything else can be set by using the object json
         * serialized blob
         */
        if (!empty($values))
        {
            $raw_values['object'] = json_encode($values);
        }

        $activity_id = $db_service->createTableRow('activities', $raw_values);
        
        return $this->getActivity($activity_id);
    }

}

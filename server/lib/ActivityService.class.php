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

        $raw_values = array();
        $raw_values['title'] = $values['title'];
        
        $valid_object = @json_decode($values['object']);
        if ($valid_object === null && $values['object'] !== 'null')
        {
            throw new Exception('Invalid json in object!');
        }
        
        $raw_values['object'] = $values['object'];
        $raw_values['actor_id'] = $actor->getId();
        $raw_values['stream_id'] = $stream->getId();

        $activity_id = $db_service->createTableRow('activities', $raw_values);
        
        return $this->getActivity($activity_id);
    }

}

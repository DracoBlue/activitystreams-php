<?php
class FeedService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Feeds';
    }

    public function getResourceNameSingularized()
    {
        return 'Feed';
    }

    public function convertResourceToJson(Feed $feed)
    {
        $converted_activities = array();

        foreach ($feed->getActivities() as $activity)
        {
            $activity_data = $activity->getValues();

            $converted_activities[] = $activity_data;
        }

        return json_encode(array('items' => $converted_activities));
    }

    /**
     * @return Feed
     */
    public function getFeed($object_id, array $values = array())
    {
        if (!isset($values['offset']) || !is_numeric($values['offset']) || $values['offset'] < 0)
        {
            throw new Exception('Invalid offset, only numbers higher then -1 are allowed!');
        }
        if (!isset($values['limit']) || !is_numeric($values['limit']) || $values['limit'] < 1)
        {
            throw new Exception('Invalid limit, only numbers higher then 0 are allowed!');
        }

        $offset = (int)$values['offset'];
        $limit = (int)$values['limit'];

        if ($limit > 100)
        {
            throw new Exception('Cannot retrieve more then 100 elements at once!');
        }

        $db_service = Services::get('Database');
        $rows = $db_service->getTableRows('activities', '
            (
                stream_id IN (SELECT id FROM activity_streams WHERE auto_subscribe = 1)
                AND stream_id NOT IN (SELECT stream_id FROM activity_stream_unsubscriptions WHERE object_id = ?)
            )
            OR
            (
                stream_id IN (SELECT stream_id FROM activity_stream_subscriptions WHERE object_id = ?)
            )
            
            ORDER BY published DESC LIMIT ' . $offset . ', ' . $limit . '
        ', array(
            $object_id,
            $object_id
        ));
        return new Feed($rows);
    }

}

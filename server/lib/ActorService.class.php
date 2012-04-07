<?php
class ActorService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Actors';
    }

    public function getResourceNameSingularized()
    {
        return 'Actor';
    }

    public function convertResourceToJson(Actor $actor)
    {
        $values = $actor->getValues();
        $values['id'] = $actor->getId();
        $values['displayName'] = $actor->getName();
        $values['objectType'] = $actor->getObjectType();
        $values['links'] = array(
            array(
                'rel' => 'feed',
                'href' => Config::get('endpoint_base_url') . 'feed/' . urlencode($actor->getId())
            ),
            array(
                'rel' => 'subscriptions',
                'href' => Config::get('endpoint_base_url') . 'subscription?actor_id=' . urlencode($actor->getId())
            )
        );

        return json_encode($values);
    }

    /**
     * @return Actor[]
     */
    public function getActors(array $values)
    {
        $db_service = Services::get('Database');

        $actors = array();
        foreach ($db_service->getTableRows('activity_stream_actors') as $row)
        {
            $actors[] = new Actor($row);
        }

        return $actors;
    }

    /**
     * @return Actor
     */
    public function getActor($actor_id, array $values = array())
    {
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('activity_stream_actors', 'id = ?', array($actor_id));
        return new Actor($row);
    }

    /**
     * @return Actor
     */
    public function deleteActor($actor_id, array $values = array())
    {
        $db_service = Services::get('Database');

        $actor = $this->getActor($actor_id);

        $db_service->deleteTableRows('activity_stream_subscriptions', 'actor_id = ?', array($actor_id));
        $db_service->deleteTableRows('activity_stream_unsubscriptions', 'actor_id = ?', array($actor_id));

        $db_service->deleteTableRow('activity_stream_actors', 'id = ?', array($actor_id));

        return $actor;
    }

    /**
     * @return Actor
     */
    public function postActor(array $values)
    {
        $db_service = Services::get('Database');
        $raw_values = array();
        
        $raw_values['name'] = $values['name'];
        unset($values['name']);

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

        $actor_id = $db_service->createTableRow('activity_stream_actors', $raw_values);

        return $this->getActor($actor_id);
    }

}

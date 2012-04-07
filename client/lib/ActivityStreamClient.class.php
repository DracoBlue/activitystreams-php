<?php

class ActivityStreamClient
{
    protected $endpoint_url = null;
    protected $json_client = null;

    protected $api_links = array();

    function __construct($endpoint_url)
    {
        $this->endpoint_url = $endpoint_url;
        $this->json_client = Services::get('JsonHttpClient');

        $this->api_links = $this->json_client->get($this->endpoint_url);
    }

    protected function getApiLink($name)
    {
        return $this->getLinkInObject($this->api_links, $name);
    }

    protected function getLinkInObject($object, $name)
    {
        foreach ($object['links'] as $link_data)
        {
            if ($link_data['rel'] == $name)
            {
                return $link_data['href'];
            }
        }

        throw new Exception('Cannot find link with rel ' . $name);
    }

    /**
     * @return array data about the stream
     */
    public function createStream($name, $auto_subscribe)
    {
        $stream_data = $this->json_client->post($this->getApiLink('streams'), array(
            'name' => $name,
            'auto_subscribe' => $auto_subscribe ? '1' : '0'
        ));

        return $stream_data;
    }

    /**
     * @return array the data about the stream
     */
    public function getStreamById($stream_id)
    {
        $streams_data = $this->json_client->get($this->getApiLink('streams'), array('stream_id' => $stream_id));

        if (count($streams_data) > 0)
        {
            return $streams_data[0];
        }
        
        throw new Exception('Cannot find stream with id ' . $stream_id);
    }
    
    public function deleteStream(array $stream)
    {
        $stream_data = $this->json_client->delete($this->getApiLink('streams') . '/' . $stream['id']);
        return $stream_data;
    }

    /**
     * @return array the data about the actor
     */
    public function createActor(array $options)
    {
        $actor_data = $this->json_client->post($this->getApiLink('actors'), $options);

        return $actor_data;
    }

    /**
     * @return array the data about the actor
     */
    public function getActorById($actor_id)
    {
        $actors_data = $this->json_client->get($this->getApiLink('actors'), array('actor_id' => $actor_id));

        if (count($actors_data) > 0)
        {
            return $actors_data[0];
        }
        
        throw new Exception('Cannot find actor with id ' . $actor_id);
    }
    
    public function deleteActor(array $actor)
    {
        $actor_data = $this->json_client->delete($this->getApiLink('actors') . '/' . $actor['id']);
        return $actor_data;
    }

    public function createActivity(array $stream, array $actor, $title, $values)
    {
        $values['title'] = $title;
        $values['actor_id'] = $actor['id'];
        
        /*
         * Create a new activity in this stream
         */
        $activity_data = $this->json_client->post($this->getLinkInObject($stream, 'activities'), $values);

        return $activity_data;
    }

    public function getFeedForActor($actor, $offset = 0, $limit = 20)
    {
        $activities_data = $this->json_client->get($this->getLinkInObject($actor, 'feed'), array(
            'offset' => $offset,
            'limit' => $limit
        ));
        return $activities_data;
    }

    public function subscribeActorToStream($actor, $stream)
    {
        $actor = $this->json_client->post($this->getLinkInObject($stream, 'subscribers'), array('actor_id' => $actor['id']));
        return $actor;
    }

    public function unsubscribeActorFromStream($actor, $stream)
    {
        $subscriptions = $this->json_client->get($this->getLinkInObject($actor, 'subscriptions'));

        foreach ($subscriptions as $subscription)
        {
            print_r($subscription);
            if ($subscription['stream_id'] == $stream['id'])
            {
                $this->json_client->delete($this->getLinkInObject($subscription, 'unsubscribe'));
            }
        }

        return $actor;
    }

}

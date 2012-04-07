<?php

require_once (dirname(__FILE__) . '/AsResource.class.php');
require_once (dirname(__FILE__) . '/AsObject.class.php');
require_once (dirname(__FILE__) . '/AsStream.class.php');
require_once (dirname(__FILE__) . '/AsSubscription.class.php');

class AsClient extends AsResource
{
    protected $endpoint_url = null;
    protected $json_client = null;
    protected $data = array();

    function __construct($endpoint_url)
    {
        $this->endpoint_url = $endpoint_url;
        $this->json_client = Services::get('JsonHttpClient');
        $this->data = $this->json_client->get($this->endpoint_url);
    }

    /**
     * @return AsStream
     */
    public function createStream($name, $auto_subscribe)
    {
        $stream_data = $this->json_client->post($this->getLink('streams'), array(
            'name' => $name,
            'auto_subscribe' => $auto_subscribe ? '1' : '0'
        ));

        return new AsStream($this, $stream_data);
    }

    /**
     * @return AsStream
     */
    public function getStreamById($stream_id)
    {
        $streams_data = $this->json_client->get($this->getLink('streams'), array('stream_id' => $stream_id));

        if (count($streams_data) > 0)
        {
            return new AsStream($this, $streams_data[0]);
        }

        throw new Exception('Cannot find stream with id ' . $stream_id);
    }

    /**
     * @return AsStream
     */
    public function deleteStream(AsStream $stream)
    {
        $this->json_client->delete($this->getLink('streams') . '/' . $stream->getId());
        return $stream;
    }

    /**
     * @return array the data about the object
     */
    public function createObject(array $options)
    {
        return new AsObject($this, $this->json_client->post($this->getLink('objects'), $options));
    }

    /**
     * @return array the data about the actor
     */
    public function getObjectById($object_id)
    {
        $objects_data = $this->json_client->get($this->getLink('objects'), array('object_id' => $object_id));

        if (count($objects_data) > 0)
        {
            return new AsObject($this, $objects_data[0]);
        }

        throw new Exception('Cannot find object with id ' . $object_id);
    }

    public function deleteObject(AsObject $object)
    {
        $this->json_client->delete($this->getLink('objects') . '/' . $object->getId());
        return $object;
    }

    public function createActivity(AsStream $stream, array $values, AsObject $actor = null, AsObject $object = null, AsObject $target = null)
    {
        if ($object !== null)
        {
            $values['object_id'] = $object->getId();
        }

        if ($actor !== null)
        {
            $values['actor_id'] = $actor->getId();
        }
        
        if ($target !== null)
        {
            $values['target_id'] = $target->getId();
        }

        /*
         * Create a new activity in this stream
         */
        return $this->json_client->post($stream->getLink('activities'), $values);
    }

    public function getFeedForObject(AsObject $object, $offset = 0, $limit = 20)
    {
        $activities_data = $this->json_client->get($object->getLink('feed'), array(
            'offset' => $offset,
            'limit' => $limit
        ));
        return $activities_data;
    }

    public function subscribeObjectToStream(AsObject $object, AsStream $stream)
    {
        $this->json_client->post($stream->getLink('subscribers'), array('object_id' => $object->getId()));
    }

    public function unsubscribeObjectFromStream(AsObject $object, $stream)
    {
        $subscriptions = $this->json_client->get($object->getLink('subscriptions'));

        foreach ($subscriptions as $subscription_data)
        {
            $subscription = new AsSubscription($this, $subscription_data);
            if ($subscription->getStreamId() == $stream->getId() && $subscription->getObjectId() == $object->getId())
            {
                $this->json_client->delete($subscription->getLink('unsubscribe'));
            }
        }
    }

}

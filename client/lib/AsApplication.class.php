<?php

class AsApplication extends AsResource
{
    protected $client = null;
    
    function __construct(AsClient $client, array $data)
    {
        parent::__construct($data);
        $this->client = $client;
    }
    
    protected function getAuth()
    {
        return array($this->getId(), $this->getSecret());
    }
    
    public function getId()
    {
        return $this->data['id'];
    }
    
    public function getSecret()
    {
        return $this->data['secret'];
    }
    
    public function getStreamById($stream_id)
    {
        $streams = $this->client->get($this->getLink('streams'), array('stream_id' => $stream_id), $this->getAuth());
        
        if (count($streams) == 0)
        {
            throw new Exception('Cannot find stream with id ' . $stream_id);
        }
        
        return new AsStream($this, $streams[0]);
    }

    public function createStream($id, array $values = array())
    {
        $values['id'] = $id;
        $stream = $this->client->post($this->getLink('streams'), $values, $this->getAuth());
        return new AsStream($this, $stream);
    }
    
    public function recreateStream($id, array $values = array())
    {
        try
        {
            $stream = $this->getStreamById($id);
            $values['id'] = $id;
            $stream = $this->client->put($stream->getLink('update'), $values, $this->getAuth());
            return new AsStream($this, $stream);
        }
        catch (Exception $exception)
        {
            return $this->createStream($id, $values);
        }
    }

    public function deleteStream(AsStream $stream)
    {
        $this->client->delete($stream->getLink('delete'), array(), $this->getAuth());
    }

    public function getObjectById($object_id)
    {
        $objects = $this->client->get($this->getLink('objects'), array('object_id' => $object_id), $this->getAuth());
        
        if (count($objects) == 0)
        {
            throw new Exception('Cannot find object with id ' . $object_id);
        }
        
        return new AsObject($this, $objects[0]);
    }

    public function createObject($id, array $values = array())
    {
        $values['id'] = $id;
        $object = $this->client->post($this->getLink('objects'), $values, $this->getAuth());
        return new AsObject($this, $object);
    }
    
    public function updateObject(AsObject $object, array $values = array())
    {
        $object = $this->client->patch($object->getLink('update'), $values, $this->getAuth());
        return new AsObject($this, $object);
    }
    
    public function recreateObject($id, array $values = array())
    {
        try
        {
            $object = $this->getObjectById($id);
            $object = $this->client->put($object->getLink('update'), $values, $this->getAuth());
            return new AsObject($this, $object);
        }
        catch (Exception $exception)
        {
            return $this->createObject($id, $values);   
        }
    }
    
    public function deleteObject(AsObject $object)
    {
        $this->client->delete($object->getLink('delete'), array(), $this->getAuth());
    }
    
    public function createActivityInStream(AsStream $stream, array $values, AsObject $actor = null, AsObject $object = null, AsObject $target = null)
    {
        $values['stream_id'] = $stream->getId();
        if ($actor)
        {
            $values['actor_id'] = $actor->getId();
        }
        if ($object)
        {
            $values['object_id'] = $object->getId();
        }
        if ($target)
        {
            $values['target_id'] = $target->getId();
        }
        $values['stream_id'] = $stream->getId();
        $this->client->post($stream->getLink('activities'), $values, $this->getAuth());
    }
    
    public function getFeedForObject(AsObject $object, $offset = 0, $limit = 20)
    {
        $values = array(
            'offset' => $offset,
            'limit' => $limit
        );
        
        $raw_feed = $this->client->get($object->getLink('feed'), $values, $this->getAuth());
        
        $activities = array();
        
        foreach ($raw_feed['items'] as $raw_activity)
        {
            $activities[] = new AsActivity($this, $raw_activity);
        }
        
        return $activities;
    }
    
    public function subscribeObjectToStream(AsObject $object, AsStream $stream)
    {
        $this->client->post($stream->getLink('subscribers'), array('object_id' => $object->getId()), $this->getAuth());
    }

    public function unsubscribeObjectFromStream(AsObject $object, $stream)
    {
        $subscriptions = $this->client->get($object->getLink('subscriptions'), array(), $this->getAuth());

        foreach ($subscriptions as $subscription_data)
        {
            $subscription = new AsSubscription($this, $subscription_data);
            if ($subscription->getStreamId() == $stream->getId() && $subscription->getObjectId() == $object->getId())
            {
                $this->client->delete($subscription->getLink('unsubscribe'), array(), $this->getAuth());
            }
        }
    }


}


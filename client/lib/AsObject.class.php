<?php

class AsObject extends AsResource
{
    protected $application = null;
    
    function __construct(AsApplication $application, array $data)
    {
        parent::__construct($data);
        $this->application = $application;
    }
    
    public function getId()
    {
        return $this->data['id'];
    }
    
    public function getUrl()
    {
        if (!isset($this->data['url']))
        {
            return null;
        }
        
        return $this->data['url'];
    }
    
    public function getObjectType()
    {
        if (!isset($this->data['objectType']))
        {
            return null;
        }
        
        return $this->data['objectType'];
    }
    
    public function getValues()
    {
        $data = $this->data;
        unset($data['objectType']);
        unset($data['url']);
        unset($data['links']);
        unset($data['id']);
        return $data;
    }
    
    public function getFeed($offset = 0, $limit = 20, AsActivity $before_activity = null)
    {
        return $this->application->getFeedForObject($this, $offset, $limit, $before_activity);
    }
    
    public function subscribeToStream(AsStream $stream)
    {
        return $this->application->subscribeObjectToStream($this, $stream);
    }
    
    public function unsubscribeFromStream(AsStream $stream)
    {
        return $this->application->unsubscribeObjectFromStream($this, $stream);
    }
}


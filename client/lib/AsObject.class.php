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
    
    public function getFeed($offset = 0, $limit = 20)
    {
        return $this->application->getFeedForObject($this, $offset, $limit);
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


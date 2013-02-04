<?php

class AsActivity extends AsResource
{
    protected $application = null;
    
    function __construct(AsApplication $application, array $data)
    {
        parent::__construct($data);
        $this->application = $application;
    }
    
    function getActorId()
    {
        if (isset($this->data['actor']))
        {
            return $this->data['actor']['id'];
        }
        
        return null;
    }
    
    function getActor()
    {
        $actor_id = $this->getActorId();
        
        if ($actor_id)
        {
            return $this->application->getObjectById($actor_id);
        }
        
        throw new Exception('This activity does not have an actor');
    }
    
    function getTargetId()
    {
        if (isset($this->data['target']))
        {
            return $this->data['target']['id'];
        }
        
        return null;
    }
    
    function getTarget()
    {
        $target_id = $this->getTargetId();
        
        if ($target_id)
        {
            return $this->application->getObjectById($target_id);
        }
        
        throw new Exception('This activity does not have a target');
    }
    
    function getObjectId()
    {
        if (isset($this->data['object']))
        {
            return $this->data['object']['id'];
        }
        
        return null;
    }
    
    function getObject()
    {
        $object_id = $this->getObjectId();
        
        if ($object_id)
        {
            return $this->application->getObjectById($object_id);
        }
        
        throw new Exception('This activity does not have an object');
    }
}


<?php
class Activity
{
    protected $data = array();
    
    function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function getPublished()
    {
        $timestamp = new DateTime($this->data['timestamp']);
        
        return $timestamp->format(DateTime::RFC3339);
    }
    
    public function getId()
    {
        return $this->data['id'];
    }
    
    public function getTitle()
    {
        return $this->data['title'];
    }
    
    public function getActorId()
    {
        return $this->data['actor_id'];
    }
    
    public function getObject()
    {
        $object = json_decode($this->data['object'], true);
        
        if (isset($this->data['object_type']) && $this->data['object_type'])
        {
            $object['objectType'] = $this->data['object_type'];
        }
        
        return $object;
    }
}

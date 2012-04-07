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
    
    public function getObjectId()
    {
        return $this->data['object_id'];
    }
    
    public function getTargetId()
    {
        return $this->data['target_id'];
    }
    
    public function getValues()
    {
        $values = json_decode($this->data['values'], true);
        return $values;
    }
}

<?php
class Activity
{
    protected $data = array();
    
    function __construct(array $data)
    {
        $this->data = $data;
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
        return json_decode($this->data['object'], true);
    }
}

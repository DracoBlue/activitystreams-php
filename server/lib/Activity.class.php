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
        $published = new DateTime($this->data['published']);
        
        return $published->format(DateTime::RFC3339);
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
        $object_service = Services::get('Object');
        
        $values = json_decode($this->data['values'], true);
        $values['published'] = $this->getPublished();
        $values['title'] = $this->getTitle();
        $values['id'] = $this->getId();
        
        if ($this->getActorId())
        {
            $values['actor'] = $object_service->getObject($this->getActorId())->getValues();
        }
        
        if ($this->getObjectId())
        {
            $values['object'] = $object_service->getObject($this->getObjectId())->getValues();
        }
        
        if ($this->getTargetId())
        {
            $values['target'] = $object_service->getObject($this->getTargetId())->getValues();
        }
        
            
        return $values;
    }
}

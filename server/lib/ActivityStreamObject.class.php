<?php

class ActivityStreamObject
{
    protected $data = array();
    
    function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function getValues()
    {
        $values = json_decode($this->data['values'], true);
        
        $values['id'] = $this->getId();
        
        if ($this->getObjectType())
        {
            $values['objctType'] = $this->getObjectType();
        }
        
        if ($this->getUrl())
        {
            $values['url'] = $this->getUrl();
        }
        
        return $values;
    }
    
    public function getId()
    {
        return $this->data['id'];
    }
    
    public function getApplicationId()
    {
        return $this->data['application_id'];
    }
    
    public function getUrl()
    {
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
}

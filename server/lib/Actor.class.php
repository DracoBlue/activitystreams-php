<?php
class Actor
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
    
    public function getName()
    {
        return $this->data['name'];
    }
    
    public function getObjectType()
    {
        return $this->data['object_type'];
    }
    
    public function getValues()
    {
        if (isset($this->data['values']) && $this->data['values'] !== null)
        {
            return json_decode($this->data['values'], true);
        }
        
        return array();
    }
}

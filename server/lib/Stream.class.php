<?php
class Stream
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
    
    public function getApplicationId()
    {
        return $this->data['application_id'];
    }
    
    public function getName()
    {
        return $this->data['name'];
    }
    
    public function isAutoSubscribe()
    {
        return $this->data['auto_subscribe'] == '1' ? true : false;
    }
}

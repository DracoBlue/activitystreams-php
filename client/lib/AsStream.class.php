<?php

class AsStream extends AsResource
{
    protected $application = null;
    
    function __construct(AsApplication $application, array $data)
    {
        parent::__construct($data);
        $this->application = $application;
    }
    
    public function createActivity(array $values, AsObject $actor = null, AsObject $object = null, AsObject $target = null)
    {
        return $this->application->createActivityInStream($this, $values, $actor, $object, $target);
    }
    
    public function getId()
    {
        return $this->data['id'];
    }
}


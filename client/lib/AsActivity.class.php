<?php

class AsActivity extends AsResource
{
    protected $application = null;
    
    function __construct(AsApplication $application, array $data)
    {
        parent::__construct($data);
        $this->application = $application;
    }
}


<?php

class AsSubscription extends AsResource
{
    protected $data = array();
    protected $client = null;
    
    function __construct(AsClient $client, array $data)
    {
        $this->client = $client;
        $this->data = $data;
    }    
    
    public function getObjectId()
    {
        return $this->data['object_id'];
    }
    
    public function getStreamId()
    {
        return $this->data['stream_id'];
    }
}

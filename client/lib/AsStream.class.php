<?php

class AsStream extends AsResource
{
    protected $data = array();
    protected $client = null;
    
    function __construct(AsClient $client, array $data)
    {
        $this->client = $client;
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
    
    public function delete()
    {
        $this->client->deleteStream($this);
    }
}

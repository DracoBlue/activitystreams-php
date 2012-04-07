<?php

class AsObject extends AsResource
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
    
    public function getTitle()
    {
        return $this->data['title'];
    }
    
    public function getUrl()
    {
        return $this->data['url'];
    }
    
    public function delete()
    {
        $this->client->deleteObject($this);
    }
}

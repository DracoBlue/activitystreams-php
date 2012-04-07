<?php
class Feed
{
    protected $rows = array();
    
    function __construct(array $rows)
    {
        $this->rows = $rows;
    }
    
    public function getActivities()
    {
        $activities = array();
        
        foreach ($this->rows as $row)
        {
            $activities[] = new Activity($row);
        }
        
        return $activities;
    }
}

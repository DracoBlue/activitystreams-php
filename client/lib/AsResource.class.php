<?php

class AsResource
{
    public function getLink($rel)
    {
        if (isset($this->data) && isset($this->data['links']))
        {
            foreach ($this->data['links'] as $link_data)
            {
                if ($link_data['rel'] == $rel)
                {
                    return $link_data['href'];
                }
            }
        }
        
        throw new Exception('Cannot find link: ' . $rel . ' on this resource!');
    }
}

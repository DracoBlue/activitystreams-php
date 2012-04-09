<?php
class StreamService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Streams';
    }

    public function getResourceNameSingularized()
    {
        return 'Stream';
    }

    public function convertResourceToJson(Stream $stream)
    {
        return json_encode(array(
            'id' => $stream->getId(),
            'name' => $stream->getName(),
            'links' => array(
                array(
                    'rel' => 'delete',
                    'href' => Config::get('endpoint_base_url') . 'stream/' . urlencode($stream->getId()) . '?application_id=' . urlencode($stream->getApplicationId())
                ),
                array(
                    'rel' => 'activities',
                    'href' => Config::get('endpoint_base_url') . 'activity?application_id=' . urlencode($stream->getApplicationId()) . '&stream_id=' . urlencode($stream->getId())
                ),
                array(
                    'rel' => 'subscribers',
                    'href' => Config::get('endpoint_base_url') . 'subscription?application_id=' . urlencode($stream->getApplicationId()) . '&stream_id=' . urlencode($stream->getId())
                )
            )
        ));
    }

    /**
     * @return Stream[]
     */
    public function getStreams(array $values)
    {
        $db_service = Services::get('Database');

        if (!isset($values['application_id']))
        {
            throw new Exception('Cannot search for streams if no application_id is given!');    
        }
        
        if (!isset($values['stream_id']))
        {
            throw new Exception('Cannot search for streams if no stream_id is given!');    
        }
        
        $stream_id = $values['stream_id'];
        $application_id = $values['application_id'];

        $streams = array();
        foreach ($db_service->getTableRows('streams', 'id = ? and application_id = ?', array($stream_id, $application_id)) as $row)
        {
            $streams[] = new Stream($row);
        }

        return $streams;
    }

    /**
     * @return Stream
     */
    public function getStream($stream_id, array $values = array())
    {
        if (!isset($values['application_id']))
        {
            throw new Exception('Cannot search for streams if no application_id is given!');    
        }
        
        $application_id = $values['application_id'];
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('streams', 'id = ? AND application_id = ?', array($stream_id, $application_id));
        return new Stream($row);
    }

    public function deleteStream($stream_id, array $values = array())
    {
        $db_service = Services::get('Database');
        $stream = $this->getStream($stream_id, $values);
        $application_id = $stream->getApplicationId();

        $db_service->deleteTableRows('subscriptions', 'stream_id = ? AND application_id = ?', array($stream_id, $application_id));
        $db_service->deleteTableRows('unsubscriptions', 'stream_id = ? AND application_id = ?', array($stream_id, $application_id));
        $db_service->deleteTableRows('activities', 'stream_id = ? AND application_id = ?', array($stream_id, $application_id));
        $db_service->deleteTableRow('streams', 'id = ? AND application_id = ?', array($stream_id, $application_id));
    }

    /**
     * @return Stream
     */
    public function postStream(array $values)
    {
        $db_service = Services::get('Database');

        $raw_values = array();
        
        $application_id = $values['application_id'];
        $raw_values['application_id'] = $application_id;
        $raw_values['id'] = $values['id'];
        
        if (isset($values['name']))
        {
            $raw_values['name'] = $values['name'];
        }

        if (!isset($values['auto_subscribe']))
        {
            $values['auto_subscribe'] = '1';
        }

        $raw_values['auto_subscribe'] = $values['auto_subscribe'] ? 1 : 0;

        $stream_id = $db_service->createTableRow('streams', $raw_values);

        return $this->getStream($stream_id, array('application_id' => $application_id));
    }

}

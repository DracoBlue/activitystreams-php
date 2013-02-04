<?php
class StreamService extends HttpResourceService
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
            'auto_subscribe' => $stream->isAutoSubscribe(),
            'links' => array(
                array(
                    'rel' => 'update',
                    'href' => Config::get('endpoint_base_url') . 'stream/' . urlencode($stream->getId())
                ),
                array(
                    'rel' => 'delete',
                    'href' => Config::get('endpoint_base_url') . 'stream/' . urlencode($stream->getId())
                ),
                array(
                    'rel' => 'activities',
                    'href' => Config::get('endpoint_base_url') . 'activity?stream_id=' . urlencode($stream->getId())
                ),
                array(
                    'rel' => 'subscribers',
                    'href' => Config::get('endpoint_base_url') . 'subscription?stream_id=' . urlencode($stream->getId())
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

        if (!isset($values['stream_id']))
        {
            throw new Exception('Cannot search for streams if no stream_id is given!');    
        }
        
        $stream_id = $values['stream_id'];
        $application_id = $this->getAuthenticatedApplicationId();

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
        $application_id = $this->getAuthenticatedApplicationId();
        
        $db_service = Services::get('Database');
        $row = $db_service->getTableRow('streams', 'id = ? AND application_id = ?', array($stream_id, $application_id));
        return new Stream($row);
    }

    public function deleteStream($stream_id, array $values = array())
    {
        $application_id = $this->getAuthenticatedApplicationId();
        
        $db_service = Services::get('Database');
        $stream = $this->getStream($stream_id);

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
        
        $application_id = $this->getAuthenticatedApplicationId();
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
    
    /**
     * @return Stream
     */
    public function putStream($stream_id, array $values)
    {
        $stream = $this->getStream($stream_id);
        $db_service = Services::get('Database');

        $application_id = $this->getAuthenticatedApplicationId();
        
        if (!isset($values['name']))
        {
            $values['name'] = '';
        }

        if (!isset($values['auto_subscribe']))
        {
            $values['auto_subscribe'] = '1';
        }

        $values['auto_subscribe'] = $values['auto_subscribe'] ? 1 : 0;
        
        $parameters = array();
        $parameters[] = $values['auto_subscribe'];
        $parameters[] = $values['name'];
        $parameters[] = $stream->getId();
        $parameters[] = $application_id;

        $db_service->updateTableRows('streams', '`auto_subscribe` = ?, `name` = ?', 'id = ? AND application_id = ?', $parameters);
        
        return $this->getStream($stream_id, array('application_id' => $application_id));
    }

}

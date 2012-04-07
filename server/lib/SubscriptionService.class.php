<?php
class SubscriptionService implements HttpResourceService
{
    public function getResourceNamePluralized()
    {
        return 'Subscriptions';
    }

    public function getResourceNameSingularized()
    {
        return 'Subscription';
    }

    public function convertResourceToJson(array $subscription)
    {
        return json_encode(array(
            'stream_id' => $subscription['stream_id'],
            'object_id' => $subscription['object_id'],
            'links' => array(
                array(
                    'rel' => 'subscribers',
                    'href' => Config::get('endpoint_base_url') . 'subscription?&stream_id=' . urlencode($subscription['stream_id']) . '&object_id=' . urlencode($subscription['object_id'])
                ),
                array(
                    'rel' => 'unsubscribe',
                    'href' => Config::get('endpoint_base_url') . 'subscription/' . urlencode($subscription['stream_id']) . '?object_id=' . urlencode($subscription['object_id'])
                )
            )
        ));
    }

    public function getSubscriptions(array $values = array())
    {
        if (!isset($values['object_id']))
        {
            throw new Exception('Cannot retrieve the subscribers of a stream, without a given object_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');

        $object_id = $values['object_id'];

        $rows = $db_service->getTableRows('activity_streams', '
            (
                id IN (SELECT id FROM activity_streams WHERE auto_subscribe = 1)
                AND id NOT IN (SELECT stream_id FROM activity_stream_unsubscriptions WHERE object_id = ?)
            )
            OR
            (
                id IN (SELECT stream_id FROM activity_stream_subscriptions WHERE object_id = ?)
            )
        ', array(
            $object_id,
            $object_id
        ));

        $subscriptions = array();

        foreach ($rows as $row)
        {
            $subscriptions[] = array(
                'object_id' => $object_id,
                'stream_id' => $row['id']
            );
        }

        return $subscriptions;
    }

    public function deleteSubscription($stream_id, array $values = array())
    {
        if (!isset($values['object_id']))
        {
            throw new Exception('Cannot remove subscription, without a given object_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');
        $object_service = Services::get('Object');

        $stream = $stream_service->getStream($stream_id);
        $object = $object_service->getObject($values['object_id']);

        $db_service->deleteTableRows('activity_stream_subscriptions', 'object_id = ? AND stream_id = ?', array(
            $object->getId(),
            $stream->getId()
        ));

        if ($stream->isAutoSubscribe())
        {
            $db_service->createTableRow('activity_stream_unsubscriptions', array(
                'object_id' => $object->getId(),
                'stream_id' => $stream->getId(),
            ));
        }
    }

    public function postSubscription(array $values = array())
    {
        if (!isset($values['object_id']))
        {
            throw new Exception('Cannot add a new subscription, without a given object_id!');
        }

        if (!isset($values['stream_id']))
        {
            throw new Exception('Cannot add a new subscription, without a given stream_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');
        $object_service = Services::get('Object');

        $stream = $stream_service->getStream($values['stream_id']);
        $object = $object_service->getObject($values['object_id']);

        $db_service->deleteTableRows('activity_stream_unsubscriptions', 'object_id = ? AND stream_id = ?', array(
            $object->getId(),
            $stream->getId()
        ));

        if (!$stream->isAutoSubscribe())
        {
            $db_service->createTableRow('activity_stream_subscriptions', array(
                'object_id' => $object->getId(),
                'stream_id' => $stream->getId(),
            ));
        }
    }

}

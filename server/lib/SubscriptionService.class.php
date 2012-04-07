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
            'actor_id' => $subscription['actor_id'],
            'links' => array(
                array(
                    'rel' => 'subscribers',
                    'href' => Config::get('endpoint_base_url') . 'subscription?&stream_id=' . urlencode($subscription['stream_id']) . '&actor_id=' . urlencode($subscription['actor_id'])
                ),
                array(
                    'rel' => 'unsubscribe',
                    'href' => Config::get('endpoint_base_url') . 'subscription/' . urlencode($subscription['stream_id']) . '?actor_id=' . urlencode($subscription['actor_id'])
                )
            )
        ));
    }

    public function getSubscriptions(array $values = array())
    {
        if (!isset($values['actor_id']))
        {
            throw new Exception('Cannot retrieve the subscribers of a stream, without a given actor_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');

        $actor_id = $values['actor_id'];

        $rows = $db_service->getTableRows('activity_streams', '
            (
                id IN (SELECT id FROM activity_streams WHERE auto_subscribe = 1)
                AND id NOT IN (SELECT stream_id FROM activity_stream_unsubscriptions WHERE actor_id = ?)
            )
            OR
            (
                id IN (SELECT stream_id FROM activity_stream_subscriptions WHERE actor_id = ?)
            )
        ', array(
            $actor_id,
            $actor_id
        ));

        $subscriptions = array();

        foreach ($rows as $row)
        {
            $subscriptions[] = array(
                'actor_id' => $actor_id,
                'stream_id' => $row['id']
            );
        }

        return $subscriptions;
    }

    public function deleteSubscription($stream_id, array $values = array())
    {
        if (!isset($values['actor_id']))
        {
            throw new Exception('Cannot remove subscription, without a given actor_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');
        $actor_service = Services::get('Actor');

        $stream = $stream_service->getStream($stream_id);
        $actor = $actor_service->getActor($values['actor_id']);

        $db_service->deleteTableRows('activity_stream_subscriptions', 'actor_id = ? AND stream_id = ?', array(
            $actor->getId(),
            $stream->getId()
        ));

        if ($stream->isAutoSubscribe())
        {
            $db_service->createTableRow('activity_stream_unsubscriptions', array(
                'actor_id' => $actor->getId(),
                'stream_id' => $stream->getId(),
            ));
        }

        return array(
            'actor_id' => $actor->getId(),
            'stream_id' => $stream->getId(),
        );
    }

    public function postSubscription(array $values = array())
    {
        if (!isset($values['actor_id']))
        {
            throw new Exception('Cannot add a new subscription, without a given actor_id!');
        }

        if (!isset($values['stream_id']))
        {
            throw new Exception('Cannot add a new subscription, without a given stream_id!');
        }

        $db_service = Services::get('Database');
        $stream_service = Services::get('Stream');
        $actor_service = Services::get('Actor');

        $stream = $stream_service->getStream($values['stream_id']);
        $actor = $actor_service->getActor($values['actor_id']);

        $db_service->deleteTableRows('activity_stream_unsubscriptions', 'actor_id = ? AND stream_id = ?', array(
            $actor->getId(),
            $stream->getId()
        ));

        if (!$stream->isAutoSubscribe())
        {
            $db_service->createTableRow('activity_stream_subscriptions', array(
                'actor_id' => $actor->getId(),
                'stream_id' => $stream->getId(),
            ));
        }

        return array(
            'actor_id' => $actor->getId(),
            'stream_id' => $stream->getId(),
        );
    }

}

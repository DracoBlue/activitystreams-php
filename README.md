# activitystreams.php

This is an activity stream server and client. It's intended to implement a RESTful service to create, publish, (un)subscribe activity streams (according to the "JSON Activity Streams 1.0"[1]).

  [1]: http://activitystrea.ms/specs/json/1.0/
  
## Terminology

A **Stream** contains multiple **Activities**. Those **Activities** are posted by **Actors**. An **Actor** can subscribe/unsubscribe to/from multiple **Streams**. The **Actor** can pull the most recent **Activities** (from the Streams he **subscribed** to) by fetching his **Feed**.

A **Stream** can be created with **auto_subscribe** attribute, which marks it as **subscribed** by all **Actors**. **Actors** are still able to **unsubscribe** these **Streams**.

## Example

The endpoint for the client is here:

    $client = new ActivityStreamClient('http://localhost/server/pub/index.php/');
    
To create a new stream, choose if you want to make it public or private. A public stream will automatically be subscribed by all actors.

    // every actor subscribes this stream!
    $public_stream = $client->createStream("A Public Stream", true);
    // actors have to subscribe to this stream manually!
    $private_stream = $client->createStream("A Private Stream", false);

To create a new reader or writer for the stream, you have to create an actor.
    
    $actor1 = $client->createActor(array('name' => 'User1'));

Now you can write with that actor to any stream (it does not matter if the actor subscribed to this stream or not).

    $public_activity = $client->createActivity($public_stream, $actor1, 'I posted a (public) new link', array('url' => 'http://example.org'));
    $private_activity = $client->createActivity($private_stream, $actor1, 'I posted a (private) new link', array('url' => 'http://example.org'));

For now the actor would only get the data from the public_stream, so you have to subscribe to the private_stream to receive the activities from this stream, too.

    $client->subscribeActorToStream($actor1, $private_stream);

Finally we can fetch the most recent posts of an actor (will be 2!).

    $feed = $client->getFeedForActor($actor1, 0, 20);

## TODO

* Implement the paper "Feeding Frenzy: Selectively Materializing Users’ Event Feeds" from <http://research.yahoo.com/pub/3203>
* Clean up the code
* Finish the tests
* Add database structure and way to create the database from scratch

## License

This work is copyright by DracoBlue (<http://dracoblue.net>) and licensed under the terms of MIT License.

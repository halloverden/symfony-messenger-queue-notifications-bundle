Configuration
=============

```yaml
hallo_verden_queue_notifications:
    message_queue_stopped_event:
        transports:
            async:
                max_handle_time: 300
                event_interval: 1800
                max_events: 2
    message_queue_has_messages_event:
        transports:
            failed:
                event_interval: 1800
                max_events: 10
                max_messages: 0
```

Here `MessageQueueStoppedEvent` will get dispatched if the message queue has stopped for 300 seconds or more 
every 1800 seconds and maximum 2 events.

Also `MessageQueueHasMessagesEvent` will get dispatched if the failed transport has messages every 1800 seconds,
maximum 10 times.

Usage
=====

Create a cronjob that executes `bin/console hallo_verden:messenger_queue_events:dispatch`
at a desired interval (i.e. every 15 seconds)

You can now create event listeners to act on these events.

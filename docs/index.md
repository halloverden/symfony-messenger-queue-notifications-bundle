Configuration
=============

```yaml
hallo_verden_messenger_queue_notifications:
    messenger_transport_has_stopped_event:
        async:
            event_intervals:
                - '1 hour'
                - '5 hours'
                - '24 hours'
            max_worker_running_interval: '5 minutes'
    messenger_transport_has_messages_event:
        failed:
            event_intervals:
                - '1 hour'
                - '5 hours'
                - '24 hours'
            max_messages: 0
```

Here `MessengerTransportHasStoppedEvent` will get dispatched if the worker for the async transport has stopped for 5 minutes or more, 
with a reminder after 1 hour, 5 hours and then every 24 hours.

Also `MessengerTransportHasMessagesEvent` will get dispatched if the failed transport has more than 0 messages,
with a reminder after 1 hour, 5 hours and then every 24 hours.

Usage
=====

Create a cronjob that executes `bin/console hallo_verden:messenger-transport-status-events:dispatch`
at a desired interval (i.e. every minute)

You can now create event listeners to act on these events.

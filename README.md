Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require halloverden/symfony-messenger-queue-notifications-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require halloverden/symfony-messenger-queue-notifications-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    HalloVerden\MessengerQueueNotificationsBundle\HalloVerdenMessengerQueueNotificationsBundle::class => ['all' => true],
];
```

### Step 3: Create and execute migration

The event_sent table needs to be created. To do this, execute:

```shell
bin/console doctrine:migrations:diff
# Check if your migration is correct first.
bin/console doctrine:migrations:migrate
```

Documentation
=============

The source of the documentation is stored in the `docs/` folder in this bundle:

[Read the documentation](docs/index.md)

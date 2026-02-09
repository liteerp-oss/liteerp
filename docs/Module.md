# 🏢 LiteERP – Module document

To develop for LiteERP you need understand to: Module (API) and Page (ReactJS). System has been supported you generate Page and Module you don't create manual.

---- 


#### Create Module 

System support create module by command line 

    php artisan app:make-module YourModule

Command file, you can consider at: 

    ./app/app/Console/MakeModule

#### Create Page 

System support create page by command line

    php artisan app:make-page YourPage 

You can see your page at: 

    ./app/resources/js/react/pages

Next step you need add into React Router at: 

    ./app/resources/js/react/admin.jsx

#### Create Event 

System support create event for module by command line

    php artisan app:make-event YourModule YourEvent

You can see new file at 

    ./app/core/YourModule/Infrastructure/Events/YourEvent.php

#### Create Listener 

System support create listener for module by command line

    php artisan app:make-listener YourModule YourListener

You can see new file at 

    ./app/core/YourModule/Infrastructure/Listeners/YourListener.php

#### Create Broadcast  

System support create broadcast for module by command line

    php artisan app:make-broadcast YourModule YourBroadcast

You can see new file at 

    ./app/core/YourModule/Infrastructure/Broadcasts/YourBroadcast.php 

---- 

## Permission

While you add new module and you wanna set permission for action then you need steps: 


#### Register event 

Default Laravel support class `Event` but system support for you create a common Event for your module to easy manage and reuse, you only define event name and data 

    Event::dispatch('erp.yourmodule.create', array $data)

At here `yourmodule` and action will be `create` or `update` or `index` ... etc.

#### Register listener 

Default Laravel support class `Event` but system support for you create a common Listeners for your module to easy manage and reuse, you only define to listener event of another event module. 

    Event::listener('erp.module.*', function(string $eventName, array $data))

#### Add permission 

To add permission you need go to `BusinessRole` module and add config example: 

    erp.yourmodule.create,
    erp.yourmodule.update,
    erp.yourmodule.delete,
    erp.yourmodule.index,
    ...etc

## Activity Logs 

This module always listener all event but with index it's not working. Because `index` and `show` action only get data no effects. To Activity Logs can understand you need sent data on event has required fields: 

    [
        'business_id' => int,
        'user_id'     => int // user take action,
        'id'          => int // entity id,
        ...any fields 
    ]

## Notifications 

Notification don't listener all events, if you wanna to create new notification you need trigger event `create ` or if you wanna to a lots of user you need `many`: 

Send to all users has roles selected

    Event::dispatch("erp.notification.many", [
            'user_id' => int // user take action
            'business_id' => int // id business 
            'type' => string, // create | delete | update | approved ... etc
            'entity_type' => string, // any entity type purchase , order , shipping ... env
            'entity_id' => int, // entity id 
            'chanels' => array<string> // db or mail  example ['db','mail']
            'roles' => array<string> // all users has this role ['admin','manager'],
            'message' => ''
    ]);

Send to someone


    Event::dispatch("erp.notification.create", [
            'user_id' => int // user take action
            'business_id' => int // id business 
            'type' => string, // create | delete | update | approved ... etc
            'entity_type' => string, // any entity type purchase , order , shipping ... env
            'entity_id' => int, // entity id 
            'chanels' => array<string> // db or mail  example ['db','mail'],
            'message' => '' // if you use chanel db then shuold short message
    ]);

With field `message` is optional for channel `db`, but if you use chanel `mail` then it is required to send message by email.
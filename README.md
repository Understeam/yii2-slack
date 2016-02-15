# Yii2 Slack integration

Designed to send messages to slack messenger

## Installation

```bash
php composer.phar require understeam/yii2-slack:~0.1 --prefer-dist
```

Also, you should configure [incoming webhook](https://api.slack.com/incoming-webhooks) inside your Slack team.

## Usage

Configure component:

```php
...
    'components' => [
        'slack' => [
            'class' => 'understeam\slack\Client',
            'url' => '<slack incoming webhook url here>',
            'username' => 'My awesome application',
        ],
    ],
...
```

Now you can send messages right into slack channel via next command:

```php
Yii::$app->slack->send('Hello', ':thumbs_up:', [
    [
        // attachment object
        'text' => 'text of attachment',
        'pretext' => 'pretext here',
    ],
]);
```

To learn more about attachments, [read slack documentation](https://api.slack.com/incoming-webhooks)

Also you can use slack as a logger:

```php
...
'components' => [
    'log' => [
        'targets' => [
            [
                'class' => 'understeam\slack\LogTarget',
                'logVars' => [],
                'except' => ['yii\web\*', 'api\components\*'],
                'levels' => ['error'],
            ],
        ],
    ],
],
...
```


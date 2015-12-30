<?php

$logDir = dirname(dirname(__FILE__)) . '/storage/logs/';

return [
    'user' => [
        'handle' => 'file',
        'path' => $logDir . 'user.log',
    ],

    'event' => [
        'handle' => 'file',
        'path' => $logDir . 'event.log',
    ],

    'sys' => [
        'handle' => 'file',
        'path' => $logDir . 'sys.log',
    ],

    'middleware' => [
        'handle' => 'file',
        'path' => $logDir . 'middleware.log',
    ],

    'job' => [
        'handle' => 'file',
        'path' => $logDir . 'job.log',
    ],

];


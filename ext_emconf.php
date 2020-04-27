<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Overlay option for Insert Records element',
    'description' => 'Let editors choose if a language overlay should be performed',
    'category' => 'frontend',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' =>
        [
            'psr-4' =>
                [
                    'GeorgRinger\\InsertRecordsOverlaymode\\' => 'Classes',
                ],
        ],
];

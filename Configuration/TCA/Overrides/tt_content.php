<?php
defined('TYPO3_MODE') || die('Access denied.');

$newFields = [
    'overlay_mode' => [
        'label' => 'Use selected records',
        'description' => 'If set, the selected record will be used and no language overlay will be performed',
        'config' => [
            'type' => 'check',
            'default' => 0,

        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $newFields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content',
    'overlay_mode', 'shortcut', 'after:records');

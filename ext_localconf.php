<?php
defined('TYPO3_MODE') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Frontend\ContentObject\RecordsContentObject::class] = [
    'className' => \GeorgRinger\InsertRecordsOverlaymode\Xclass\RecordsContentObjectXclassed::class,
];

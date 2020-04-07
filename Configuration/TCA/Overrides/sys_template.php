<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'insert_records_overlaymode',
    'Configuration/TypoScript',
    'Enable overlay mode selection'
);

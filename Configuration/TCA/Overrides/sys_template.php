<?php
declare(strict_types=1);
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'auth0',
    'Configuration/TypoScript',
    'Auth0 for TYPO3'
);

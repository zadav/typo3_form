<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_feuserstat_pi1.php', '_pi1', 'list_type', 1);

// eID
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']
        ['feuserstat'] = 'EXT:feuserstat/class.tx_feuserstat_eid.php';
?>
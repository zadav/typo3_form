<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_feuserstat_sessions'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
                'hideTable' => true,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_feuserstat_sessions.gif',
	),
);

$TCA['tx_feuserstat_tx_feuserstat_pagestats'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_tx_feuserstat_pagestats',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
                'hideTable' => true,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_feuserstat_tx_feuserstat_pagestats.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:feuserstat/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE === 'BE') {
	t3lib_extMgm::addModulePath('web_txfeuserstatM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

	t3lib_extMgm::addModule('web', 'txfeuserstatM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

?>
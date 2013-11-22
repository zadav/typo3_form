<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_feuserstat_sessions'] = array(
	'ctrl' => $TCA['tx_feuserstat_sessions']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,fe_user,session_start,session_end,hits,first_page,last_page'
	),
	'feInterface' => $TCA['tx_feuserstat_sessions']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fe_user' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.fe_user',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'session_start' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.session_start',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'eval' => 'required',
			)
		),
		'session_end' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.session_end',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '15',
				'eval' => 'required',
			)
		),
		'hits' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.hits',
			'config' => array(
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'first_page' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.first_page',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'last_page' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_sessions.last_page',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, fe_user, session_start, session_end, hits, first_page, last_page')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_feuserstat_tx_feuserstat_pagestats'] = array(
	'ctrl' => $TCA['tx_feuserstat_tx_feuserstat_pagestats']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'fe_user,sesstat_uid,page_uid,hits'
	),
	'feInterface' => $TCA['tx_feuserstat_tx_feuserstat_pagestats']['feInterface'],
	'columns' => array(
		'fe_user' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_tx_feuserstat_pagestats.fe_user',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'sesstat_uid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_tx_feuserstat_pagestats.sesstat_uid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_feuserstat_sessions',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'page_uid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_tx_feuserstat_pagestats.page_uid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'hits' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:feuserstat/locallang_db.xml:tx_feuserstat_tx_feuserstat_pagestats.hits',
			'config' => array(
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array(
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'fe_user;;;;1-1-1, sesstat_uid, page_uid, hits')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>
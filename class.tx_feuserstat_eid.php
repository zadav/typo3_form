<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Dmitry Dulepov <dmitry@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_t3lib . 'class.t3lib_befunc.php');
require_once(PATH_t3lib . 'stddb/tables.php');
require_once(t3lib_extMgm::extPath('cms', 'ext_tables.php'));

/**
 * Handles AJAX request from tx_feuserstat_pi1 plugin
 */
class tx_feuserstat_eID {
	/**
	 * Main function of the class
	 *
	 * @return string	Generated HTML
	 */
	function main() {
		// Connect database
		tslib_eidtools::connectDB();

		// Get query parameters
		$pid = intval(t3lib_div::_GP('pid'));
		$search = trim(t3lib_div::_GP('search'));
		// Get content
		$content = '';
		if ($pid && strlen($search) >= 3) {
			// Prepare & execute search, 100 items max
			$qsearch = $GLOBALS['TYPO3_DB']->fullQuoteStr($search . '%', 'fe_users');
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username,name',
				'fe_users', 'pid=' . $pid .
				' AND (username LIKE ' . $qsearch . ' OR name LIKE ' . $qsearch . ')' .
				t3lib_BEfunc::deleteClause('fe_users') . t3lib_BEfunc::BEenableFields('fe_users'),
				'', '', 100);
			$result = array();
			while (false !== ($ar = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				// Record only matching values
				foreach ($ar as $value) {
					if (stristr($value, $search) !== false) {
						$result[] = $value;
					}
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			// Sort results and create content
			if (count($result)) {
				sort($result);
				$content = '<li>' . implode('</li><li>', $result) . '</li>';
			}
		}
		// Output result
		echo '<ul> ' . $content . '</ul>';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserstat/class.tx_feuserstat_eid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserstat/class.tx_feuserstat_eid.php']);
}

$SOBE = t3lib_div::makeInstance('tx_feuserstat_eID');
$SOBE->main();

?>
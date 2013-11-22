<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 David Zaoui <dazao@smile.fr>
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

// require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Frontend user list' for the 'feuserstat' extension.
 *
 * @author	David Zaoui <dazao@smile.fr>
 * @package	TYPO3
 * @subpackage	tx_feuserstat
 */
class tx_feuserstat_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_feuserstat_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_feuserstat_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'feuserstat';	// The extension key.
	public $pi_checkCHash = TRUE;

	/**
	 * The main method of the Plugin.
	 *
	 * @param string $content The Plugin content
	 * @param array $conf The Plugin configuration
	 * @return string The content that is displayed on the website
	 */
	public function main($content, array $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

                // Check environment
                if (!isset($this->conf['usersPid'])) {
                    return $this->pi_wrapInBaseClass($this->pi_getLL('no_ts_template'));
                }
                // Init
                $this->init();
                // Add JS and CSS
                $this->addHeaderParts();

                if (t3lib_div::testInt($this->piVars['showUid'])) {
                    $content = $this->singleView();
                } else {
                    $content = $this->listView();
                }

                return $this->pi_wrapInBaseClass($content);
	}

        /**
         * Initializes plugin configuration
         *
         * @return string Generated HTML
         */
        protected function init() {
            $this->pi_initPIflexForm();
            // Get values
            $this->conf['usersPid'] = intval($this->fetchConfigurationValue('usersPid'));
            $this->conf['singlePid'] = intval($this->fetchConfigurationValue('singlePid'));
            $this->conf['listPid'] = intval($this->fetchConfigurationValue('listPid'));
            $this->conf['templateFile'] = intval($this->fetchConfigurationValue('templateFile'));

            // Set defaults if necessary
            if (!$this->conf['usersPid']) {
                $GLOBALS['TT']->setTSlogMessage(
                        'Warning: usersPid is not set in '.$this->prefixId. 'plugin. No users will be shown!',
                        2
                        );
            }

            if (!$this->conf['singlePid']) {
                $this->conf['singlePid'] = $GLOBALS['TSFE']->id;
            }

            if (!$this->conf['listPid']) {
                $this->conf['listPid'] = $GLOBALS['TSFE']->id;
            }

            if (!$this->conf['templateFile']) {
                $this->conf['templateFile'] = 'EXT:'.$this->extKey.'/res/pi1_template.html';
            }

            //Load template code
            $this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
        }

        /**
         * Shows single user card.
         *
         * @return string Generated HTML
         */
        protected function singleView() {

            $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    '*',
                    'fe_users',
                    'uid='.  intval($this->piVars['showUid'])
                        .' AND '
                        .'pid='. intval($this->conf['usersPid'])
                        .$this->cObj->enableFields('fe_users')
                    );
            $content = '';
            if (count($rows) == 0) {
                $template = $this->cObj->getSubpart($this->templateCode, '###SINGLE_VIEW_NO_USER###');
                $content = $this->cObj->substituteMarker(
                        $template,
                        '###TEXT_USER_NOT_FOUND###',
                        $this->pi_getLL('user_not_found')
                        );
            } else {
                $markers = array();
                // Load fe_users table information into $TCA. We need
                // this because we will extract labels from $TCA
                t3lib_div::loadTCA('fe_users');
                // Labels in $TCA are usually in 'LLL:EXT:...' format
                // We need language object to "decode" them
                $lang = t3lib_div::makeInstance('language');
                /* @var $lang language */
                $lang->init($GLOBALS['TSFE']->lang);
                // Next we need a new instance of tslib_cObj because
                // we will pass user record as data to this cObject
                $cObj = t3lib_div::makeInstance('tslib_cObj');
                /* @var $cObj tslib_cObj */
                $cObj->start($rows[0], 'fe_users');
                // Now create marker for each field
                foreach ($rows[0] as $field => $value) {
                    //Skip some sensitive fields
                    if (!t3lib_div::inList('password,uc,user_not_found,lockToDomain,TSconfig', field)) {
                        //Get Label
                        $label = $this->pi_getLL('field_'.$field);
                        if (!$label) {
                            // No local label, fetch it from $TCA
                            $label = $lang->sL($GLOBALS['TCA']['fe_users']['columns'][$field]['label']);
                        }
                        //Fill markers
                        $fieldUpper = strtoupper($field);
                        $markers['###TEXT_' . $fieldUpper . '###'] = $label;
                        $markers['###FIELD_' . $fieldUpper . '###'] =
                                $cObj->stdWrap(
                                        htmlspecialchars($value),
                                        $this->conf['singleView.'][$field . '_stdWrap.']
                                );
                     }
                }
                //Get template for the subpart
                $template = $this->cObj->getSubpart($this->templateCode, '###SINGLE_VIEW###');
                //Create output
                $content = $this->cObj->substituteMarkerArray($template, $markers);

            }
            return $content;
        }

        /**
         * Shows user list.
         *
         * @return string Generated HTML
         */
        protected function listView() {
            //Get List parameters
            $pageSize = t3lib_div::testInt($this->conf['listView.']['pageSize']) ?
                    intval($this->conf['listView.']['pageSize'])
                    : 10;
            $page = max(1, intval($this->piVars['page']));

            // Get template for LIST view
            $template = $this->cObj->getSubPart($this->templateCode,'###LIST###');
            // Get plain markers
            $markers = $this->listViewGetHeaderMarkers();
            // Get rows
            $subParts['###LIST_ITEM###'] = $this->listViewGetRows($template, $page, $pageSize);
            // Create pager
            $subParts['###PAGER###'] = $this->listViewGetPager($template, $page, $pageSize);

            //Compile output
            $content = $this->cObj->substituteMarkerArrayCached($template, $markers, $subParts);


            return $content;
        }

        protected function listViewGetHeaderMarkers() {
            // Prepare
            t3lib_div::loadTCA('fe_users');
            $lang = t3lib_div::makeInstance('language');
            /* @var $lang language */
            $lang->init($GLOBALS['TSFE']->lang);
            // Fill some header markers. Here we will use all registered TCA fields
            // plus two date fields to add header markers
            $markers = array(
                '###TEXT_NUMBER###' => $this->pi_getLL('text_number'),
                '###TEXT_CRDATE###' => $this->pi_getLL('field_crdate'),
                '###TEXT_TSTAMP###' => $this->pi_getLL('field_tstamp'),
                '###TEXT_LASTLOGIN###' => $this->pi_getLL('field_lastlogin'),
            );
            // Create markers
            foreach (array_keys($GLOBALS['TCA']['fe_users']['columns']) as $field) {
                $str = $this->pi_getLL('field_'.$field);
                if (!str) {
                    $str = $lang->sL($GLOBALS['TCA']['fe_users']['columns'][$field]['label']);
                }
                $markers['###TEXT_'.strtoupper($field).'###'] = $str;
            }
            return $markers;
        }

        protected function listViewGetRows($template, $page, $pageSize) {
            // Get parameters for DB call
            $sort = $this->conf['listView.']['sortField'] ? $this->conf['listView.']['sortField'] : 'username ASC';
            $number = intval($page- 1);
            $number *= intval($pageSize);
            //Prepare all nexessary objects and arrays
            $cObj = t3lib_div::makeInstance('tslib_cObj');
            $subTemplate = $this->cObj->getSubpart($template, '###LIST_ITEM###');
            /* @var $cObj tslib_cObj */
            // Get Data from DB
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                    '*',
                    'fe_users',
                    $this->getListWhere().$this->cObj->enableFields('fe_users'),
                    '',
                    $sort,
                    $number.'/'.$pageSize
                    );
            // Collect data
            $content = '';
            // Must check if we got result. We could get null due to the wrong sort field!
            if (!$res) {
                $GLOBALS['TT']->setTSlogMessage(
                        'SQL query for user records in list view has failed in '
                        .$this->prefixId
                        .' plugin. No users will be shown!',
                        2
                        );
            } else {
                while (false !== ($ar = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                    //Prepare for stdWrap
                    $cObj->start($ar, 'fe_users');
                    //Loop through fields applying stdWrap
                    $subMarkers = array(
                       '###NUMBER###' => $number,
                    );
                    foreach ($ar as $field => $value) {
                      if (!t3lib_div::inList($this->protectedFields, $field)) {
                          $subMarkers['###FIELD_###' . strtoupper($field) . '###'] =
                                  $cObj->stdWrap(htmlspecialchars($value),$this->conf['listView.'][$field.'_stdWrap']);
                      }
                    }
                    //add row to output
                    $content .= $this->cObj->substituteMarkerArray($subTemplate, $subMarkers);
                }
                // Free database result
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
            }
            return $content;
        }

        protected  function listViewGetPager($template, $page, $pageSize) {
            //Check if we need page at all
            list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                            'COUNT(*) AS t',
                            'fe_users',
                            $this->getListWhere() .
                            $this->cObj->enableFields('fe_users')
                    );
            if ($row['t'] < $pageSize) {
                //remove pager completely
                return '';
            }
            //Prepare
            $markers = array('###CURRENT_PAGE###' => $page);
            if ($page == 1) {
                //No previous page
                $markers['###LINK_PREV###'] = '';
            } else {
                $markers['###LINK_PREV###'] = $this->pi_linkTP_keepPIvars(
                        $this->pi_getLL('link_prev'),
                        array('page' => $page - 1),
                        true
                        );
            }
            if($row['t'] <=  $page*pageSize) {
                //No next link
                $markers['###LINK_NEXT###'] = '';
            } else {
                $markers['###LINK_NEXT###'] = $this->pi_linkTP_keepPIvars(
                        $this->pi_getLL('link_next'),
                        array('page' => $page + 1),
                        true
                        );
            }
            $subTemplate = $this->cObj->getSubpart($template, '###PAGER###');

            return $this->cObj->substituteMarkerArray($subTemplate, $markers);
        }

        protected function getListWhere() {
            return 'pid=' . intval($this->conf['usersPid']);
        }


        /**
         * Fetches configuration value given its name.
         * Merges flexform and TS configuration values.
         *
         * @param string $param Configuration value name
         * @return string Parameter value
         */
        protected function fetchConfigurationValue ($param) {
            $value = trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], $param));
            return $value ? $value : $this->conf[$param];
        }

        protected function addHeaderParts() {
            $key = 'EXT:' . $this->extKey . md5($this->templateCode);
            if (!isset($GLOBALS['TSFE']->additionalHeaderData[$key])) {
                $headerParts = $this->cObj->getSubpart($this->templatecode, '###HEADER_PARTS###');
                if ($headerParts) {
                    $headerParts = $this->cObj->substituteMarker(
                            $headerParts,
                            '###SITE_REL_PATH###',
                            t3lib_extMgm::siteRelPath($this->extKey));
                    $GLOBALS['TSFE']->additionalHeaderData[$key] = $headerParts;
                }
            }
        }


}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/feuserstat/pi1/class.tx_feuserstat_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/feuserstat/pi1/class.tx_feuserstat_pi1.php']);
}

?>
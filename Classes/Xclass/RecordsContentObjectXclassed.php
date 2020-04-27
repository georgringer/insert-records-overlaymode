<?php
declare(strict_types=1);

namespace GeorgRinger\InsertRecordsOverlaymode\Xclass;

/**
 * This file is part of the "record_overlaymode" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\RecordsContentObject;

class RecordsContentObjectXclassed extends RecordsContentObject
{

    /**
     * @inheritDoc
     */
    public function render($conf = [])
    {
        $useSelectedRecord = isset($conf['overlayMode.']) ? $this->cObj->stdWrap($conf['overlayMode'], $conf['overlayMode.']) : $conf['overlayMode'];
        if (!$useSelectedRecord) {
            return parent::render($conf);
        }


        // Reset items and data
        $this->itemArray = [];
        $this->data = [];

        $theValue = '';
        $originalRec = $GLOBALS['TSFE']->currentRecord;
        // If the currentRecord is set, we register, that this record has invoked this function.
        // It's should not be allowed to do this again then!!
        if ($originalRec) {
            ++$GLOBALS['TSFE']->recordRegister[$originalRec];
        }

        $tables = isset($conf['tables.']) ? $this->cObj->stdWrap($conf['tables'], $conf['tables.']) : $conf['tables'];
        if ($tables) {
            $tablesArray = array_unique(GeneralUtility::trimExplode(',', $tables, true));
            // Add tables which have a configuration (note that this may create duplicate entries)
            if (is_array($conf['conf.'])) {
                foreach ($conf['conf.'] as $key => $value) {
                    if (substr($key, -1) !== '.' && !in_array($key, $tablesArray)) {
                        $tablesArray[] = $key;
                    }
                }
            }

            // Get the data, depending on collection method.
            // Property "source" is considered more precise and thus takes precedence over "categories"
            $source = isset($conf['source.']) ? $this->cObj->stdWrap($conf['source'], $conf['source.']) : $conf['source'];
            $categories = isset($conf['categories.']) ? $this->cObj->stdWrap($conf['categories'], $conf['categories.']) : $conf['categories'];
            if ($source) {
                $this->collectRecordsFromSource($source, $tablesArray);
            } elseif ($categories) {
                $relationField = isset($conf['categories.']['relation.']) ? $this->cObj->stdWrap($conf['categories.']['relation'], $conf['categories.']['relation.']) : $conf['categories.']['relation'];
                $this->collectRecordsFromCategories($categories, $tablesArray, $relationField);
            }
            $itemArrayCount = count($this->itemArray);
            if ($itemArrayCount > 0) {
                /** @var ContentObjectRenderer $cObj */
                $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
                $cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
                $this->cObj->currentRecordNumber = 0;
                $this->cObj->currentRecordTotal = $itemArrayCount;
                foreach ($this->itemArray as $val) {
                    $row = $this->data[$val['table']][$val['id']];
                    // Perform overlays if necessary (records coming from category collections are already overlaid)
                    if ($source) {
                        // Versioning preview
                        $this->getPageRepository()->versionOL($val['table'], $row);
                        // Language overlay
//                        if (is_array($row)) {
//                            $row = $this->getPageRepository()->getLanguageOverlay($val['table'], $row);
//                        }
                    }
                    // Might be unset during the overlay process
                    if (is_array($row)) {
                        $dontCheckPid = isset($conf['dontCheckPid.']) ? $this->cObj->stdWrap($conf['dontCheckPid'], $conf['dontCheckPid.']) : $conf['dontCheckPid'];
                        if (!$dontCheckPid) {
                            $validPageId = $this->getPageRepository()->filterAccessiblePageIds([$row['pid']]);
                            $row = !empty($validPageId) ? $row : '';
                        }
                        if ($row && !$GLOBALS['TSFE']->recordRegister[$val['table'] . ':' . $val['id']]) {
                            $renderObjName = $conf['conf.'][$val['table']] ?: '<' . $val['table'];
                            $renderObjKey = $conf['conf.'][$val['table']] ? 'conf.' . $val['table'] : '';
                            $renderObjConf = $conf['conf.'][$val['table'] . '.'];
                            $this->cObj->currentRecordNumber++;
                            $cObj->parentRecordNumber = $this->cObj->currentRecordNumber;
                            $GLOBALS['TSFE']->currentRecord = $val['table'] . ':' . $val['id'];
                            $this->cObj->lastChanged($row['tstamp']);
                            $cObj->start($row, $val['table']);
                            $tmpValue = $cObj->cObjGetSingle($renderObjName, $renderObjConf, $renderObjKey);
                            $theValue .= $tmpValue;
                        }
                    }
                }
            }
        }
        $wrap = isset($conf['wrap.']) ? $this->cObj->stdWrap($conf['wrap'], $conf['wrap.']) : $conf['wrap'];
        if ($wrap) {
            $theValue = $this->cObj->wrap($theValue, $wrap);
        }
        if (isset($conf['stdWrap.'])) {
            $theValue = $this->cObj->stdWrap($theValue, $conf['stdWrap.']);
        }
        // Restore
        $GLOBALS['TSFE']->currentRecord = $originalRec;
        if ($originalRec) {
            --$GLOBALS['TSFE']->recordRegister[$originalRec];
        }
        return $theValue;
    }
}

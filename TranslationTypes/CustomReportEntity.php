<?php
/**
 * Copyright (C) InnoCraft Ltd - All rights reserved.
 *
 * NOTICE:  All information contained herein is, and remains the property of InnoCraft Ltd.
 * The intellectual and technical concepts contained herein are protected by trade secret or copyright law.
 * Redistribution of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from InnoCraft Ltd.
 *
 * You shall use this code only in accordance with the license agreement obtained from InnoCraft Ltd.
 *
 * @link https://www.innocraft.com/
 * @license For license details see https://www.innocraft.com/license
 */
namespace Piwik\Plugins\CustomTranslation\TranslationTypes;

use Piwik\API\Request;
use Piwik\Common;
use Piwik\DataTable\DataTableInterface;
use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\CustomTranslation\Dao\TranslationsDao;

class CustomReportEntity extends TranslationType
{
    const ID = 'customReportEntity';

    /**
     * @var CustomDimensionLabel
     */
    private $customDimensionLabel;
    /**
     * @var EventLabel
     */
    private $eventLabel;

    public function __construct(TranslationsDao $storage, CustomDimensionLabel $customDimensionLabel, EventLabel $eventLabel)
    {
        parent::__construct($storage);
        $this->customDimensionLabel = $customDimensionLabel;
        $this->eventLabel = $eventLabel;
    }

    public function getName()
    {
        return Piwik::translate('CustomTranslation_CustomReportName');
    }

    public function getDescription()
    {
        return Piwik::translate('CustomTranslation_CustomReportDescription');
    }

    public function getTranslationKeys()
    {
        $rows = Db::fetchAll('SELECT DISTINCT `name` from ' . Common::prefixTable('custom_reports') . ' where status = "active"');
        return array_filter(array_unique(array_column($rows, 'name')));
    }

    public function translate($returnedValue, $method, $extraInfo)
    {
        if ($method === 'CustomReports.getConfiguredReports'
            && is_array($returnedValue)) {

            if ($this->isRequestingAPIwithinUI('CustomReports.getConfiguredReports')) {
                // make sure in manage reports screen we show original name... but not when API is called independently
                return $returnedValue;
            }

            $translations = $this->getTranslations();
            foreach ($returnedValue as &$value) {
                if (isset($translations[$value['name']])) {
                    $value['name'] = $translations[$value['name']];
                }
            }
        }

        if ($method === 'CustomReports.getConfiguredReport'
            && is_array($returnedValue)) {
            if ($this->isRequestingAPIwithinUI('CustomReports.getConfiguredReport')) {
                return $returnedValue;
            }

            $translations = $this->getTranslations();
            if (isset($translations[$returnedValue['name']])) {
                $returnedValue['name'] = $translations[$returnedValue['name']];
            }
        }

        if ($method === 'CustomReports.getCustomReport'
            && $returnedValue instanceof DataTableInterface) {
            $params = ['idSite' => $extraInfo['parameters']['idSite'], 'idCustomReport' => $extraInfo['parameters']['idCustomReport']];
            $customReport = Request::processRequest('CustomReports.getConfiguredReport', $params, []);
            if ($customReport['report_type'] === 'table') {
                $dimensions = $customReport['dimensions'];
                $renameMap = array();
                foreach ($dimensions as $level => $dimension) {
                    if (strpos($dimension, 'CustomDimension') === 0) {
                        // rename custom dimension values
                        $renameMap[$level + 1] = $this->customDimensionLabel;
                    }
                    if (strpos($dimension, 'Events') === 0) {
                        // rename event values
                        $renameMap[$level + 1] = $this->eventLabel;
                    }
                }

                $returnedValue->filter('Piwik\Plugins\CustomTranslation\DataTable\Filter\RenameLabelFilter', array($renameMap));
            }
        }

        return $returnedValue;
    }

}

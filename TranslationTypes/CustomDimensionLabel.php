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
namespace Piwik\Plugins\CustomTranslations\TranslationTypes;

use Piwik\Common;
use Piwik\DataTable\DataTableInterface;
use Piwik\Db;
use Piwik\Plugins\CustomDimensions\CustomDimensions;

class CustomDimensionLabel extends TranslationType
{
    const ID = 'customDimensionLabel';

    public function getName()
    {
        return 'Tracked Custom Dimension Value';
    }

    public function getDescription()
    {
        return 'Translates the name of tracked Custom Dimension values';
    }

    public function getTranslationKeys()
    {
        return [];
    }

    public function translate($returnedValue, $method, $extraInfo)
    {
        if ($method === 'CustomDimensions.getCustomDimension' && $returnedValue instanceof DataTableInterface) {
            $renameMap = array('all' => $this->getTranslations());
            $returnedValue->filter('Piwik\Plugins\CustomTranslations\DataTable\Filter\RenameLabelFilter', array($renameMap));
        }
        return $returnedValue;
    }

}

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

use Piwik\DataTable\DataTableInterface;
use Piwik\Piwik;

class EventLabel extends TranslationType
{
    const ID = 'eventLabel';

    public function getName()
    {
        return Piwik::translate('CustomTranslation_EventValue');
    }

    public function getDescription()
    {
        return Piwik::translate('CustomTranslation_EventValueDescription');
    }

    public function getTranslationKeys()
    {
        return [];
    }

    public function translate($returnedValue, $method, $extraInfo)
    {
        if (strpos($method, 'Events.') === 0 && $returnedValue instanceof DataTableInterface) {
            $renameMap = array('all' => $this->getTranslations());
            $returnedValue->filter('Piwik\Plugins\CustomTranslation\DataTable\Filter\RenameLabelFilter', array($renameMap));
        }
        return $returnedValue;
    }

}

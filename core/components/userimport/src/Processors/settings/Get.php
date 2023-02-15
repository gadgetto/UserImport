<?php

/**
 * This file is part of the UserImport package.
 *
 * @copyright bitego (Martin Gartner)
 * @license GNU General Public License v2.0 (and later)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitego\UserImport\Processors\Settings;

use MODX\Revolution\modX;
use MODX\Revolution\Processors\Processor;

/**
 * UserImport settings get processor.
 *
 * @package userimport
 * @subpackage processors
 */

class Get extends Processor
{
    public function process()
    {
        $settings = [
            'delimiter',
            'enclosure',
            'autousername',
            'setimportmarker',
            'notifyusers',
            'mailsubject',
            'mailbody',
            'mail_format',
        ];

        // Get cached versions of system settings (getOption not getObject::modSystemSetting)
        // (MODExt.xcheckbox field needs integer typecasting)
        $data = [];
        foreach ($settings as $key) {
            $option = $this->modx->getOption('userimport.' . $key);
            $option = is_numeric($option) ? (int)$option : $option;
            $data[$key] = $option;
        }

        $response = [];
        $response['data'] = $data;
        $response['success'] = true;

        return $this->modx->toJSON($response);
    }
}

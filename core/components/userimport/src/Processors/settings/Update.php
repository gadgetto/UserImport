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

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modSystemSetting;
use MODX\Revolution\modX;

/**
 * UserImport settings update processor.
 *
 * @package userimport
 * @subpackage processors
 */

class Update extends Processor
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

        foreach ($settings as $key) {
            $value = $this->getProperty($key);
            if (isset($value)) {
                $setting = $this->modx->getObject(modSystemSetting::class, 'userimport.' . $key);
                if ($setting != null) {
                    $setting->set('value', $value);
                    $setting->save();
                } else {
                    $this->modx->log(
                        modX::LOG_LEVEL_ERROR,
                        '[UserImport] Settings Update processor: ' . $key . ' setting could not be found.'
                    );
                }
            }
        }

        // refresh part of cache (MODx 2.1.x)
        $cacheRefreshOptions = ['system_settings' => []];
        $this->modx->cacheManager->refresh($cacheRefreshOptions);

        $response = [];
        $response['data'] = $this->getProperties();
        $response['success'] = true;

        // Response array contains HTML!
        return json_encode(
            $response,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
        );
    }
}

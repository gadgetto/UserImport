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

/**
 * UserImport settings update processor
 *
 * @package userimport
 * @subpackage processors
 */

class SettingsUpdateProcessor extends modProcessor
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
        ];

        foreach ($settings as $key) {
            $value = $this->getProperty($key);
            if (isset($value)) {
                $setting = $this->modx->getObject('modSystemSetting', 'userimport.' . $key);
                if ($setting != null) {
                    $setting->set('value', $value);
                    $setting->save();
                } else {
                    $this->modx->log(
                        modX::LOG_LEVEL_ERROR,
                        '[UserImport] SettingsUpdateProcessor: ' . $key . ' setting could not be found'
                    );
                }
            }
        }

        // refresh part of cache (MODx 2.1.x)
        $cacheRefreshOptions = ['system_settings' => []];
        $this->modx->cacheManager->refresh($cacheRefreshOptions);

        $response['success'] = true;
        $response['data'] = $this->getProperties();

        // Fix/workaround for: Uncaught SyntaxError: Invalid regular expression: missing /
        // -> remove fields which could contain <tag></tag>
        unset($response['data']['mailsubject'], $response['data']['mailbody']);

        return $this->modx->toJSON($response);
    }
}
return 'SettingsUpdateProcessor';

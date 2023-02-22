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
 * UserImport settings get processor
 *
 * @package userimport
 * @subpackage processors
 */

class SettingsGetProcessor extends modProcessor
{
    public function process()
    {
        // get cached versions of system settings
        $settings = [
            'delimiter'       => $this->modx->getOption('userimport.delimiter'),
            'enclosure'       => $this->modx->getOption('userimport.enclosure'),
            'autousername'    => (bool)$this->modx->getOption('userimport.autousername'),
            'setimportmarker' => (bool)$this->modx->getOption('userimport.setimportmarker'),
            'notifyusers'     => (bool)$this->modx->getOption('userimport.notifyusers'),
            'mailsubject'     => $this->modx->getOption('userimport.mailsubject'),
            'mailbody'        => $this->modx->getOption('userimport.mailbody'),
        ];
        $response['success'] = true;
        $response['data'] = $settings;

        return $this->modx->toJSON($response);
    }
}
return 'SettingsGetProcessor';

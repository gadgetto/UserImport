<?php
/**
 * UserImport
 *
 * Copyright 2014 by bitego <office@bitego.com>
 *
 * UserImport is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * UserImport is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this software; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * UserImport settings get processor
 *
 * @package userimport
 * @subpackage processors
 */

class SettingsGetProcessor extends modProcessor {

    public function process() {

        // get cached versions of system settings
        $settings = array(
            'delimiter'       => $this->modx->getOption('userimport.delimiter'),
            'enclosure'       => $this->modx->getOption('userimport.enclosure'),
            'autousername'    => (bool)$this->modx->getOption('userimport.autousername'),
            'setimportmarker' => (bool)$this->modx->getOption('userimport.setimportmarker'),
            'notifyusers'     => (bool)$this->modx->getOption('userimport.notifyusers'),
            'mailsubject'     => $this->modx->getOption('userimport.mailsubject'),
            'mailbody'        => $this->modx->getOption('userimport.mailbody'),
        );        
        $response['success'] = true;
        $response['data'] = $settings;
        
        return $this->modx->toJSON($response);
    }

}
return 'SettingsGetProcessor';

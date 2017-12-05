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
 * UserImport Index manager controller
 *
 * @package userimport
 */

require_once dirname(dirname(dirname(__FILE__))).'/model/userimport/userimport.class.php';

class UserImportIndexManagerController extends modExtraManagerController {

    /** @var UserImport $userimport */
    public $userimport;
    
    public function initialize() {
        $this->userimport = new UserImport($this->modx);
        
        // Add custom css file to manager-page header
        $cssFile = $this->userimport->config['cssUrl'].'mgr23.css';
        $this->addCss($cssFile);
        
        // initialize UserImport Js
        $this->addJavascript($this->userimport->config['jsUrl'].'mgr/userimport.js');
        
        return parent::initialize();
    }

    public function getLanguageTopics() {
        return array('user,userimport:default');
    }
    
    public function process(array $scriptProperties = array()) {
    }
    
    public function getPageTitle() {
        return $this->modx->lexicon('userimport');
    }
    
    public function getTemplateFile() {
        return '';
    }

    public function checkPermissions() {
        return true;
    }

    public function loadCustomCssJs() {
        
        // load utilities and reusable functions
        $this->addJavascript($this->userimport->config['jsUrl'].'utils/utilities.js');
        
        // load widgets
        $this->addJavascript($this->userimport->config['jsUrl'].'mgr/widgets/import.panel.js');
        $this->addJavascript($this->userimport->config['jsUrl'].'mgr/widgets/notificationtemplate.panel.js');
        $this->addJavascript($this->userimport->config['jsUrl'].'mgr/widgets/about.panel.js');
        
        // load import panel widgets container
        $this->addLastJavascript($this->userimport->config['jsUrl'].'mgr/sections/index.panel.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function(){
            UserImport.config = '.$this->modx->toJSON($this->userimport->config).';
            UserImport.request = '.$this->modx->toJSON($_GET).';
            Ext.onReady(function(){MODx.add("userimport-panel-index");});
        });
        </script>');
    }
}

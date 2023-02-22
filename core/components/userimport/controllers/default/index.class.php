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
 * UserImport Index manager controller
 *
 * @package userimport
 */

require_once dirname(dirname(dirname(__FILE__))) . '/model/userimport/userimport.class.php';

class UserImportIndexManagerController extends modExtraManagerController
{
    /** @var UserImport $userimport */
    public $userimport;

    public function initialize()
    {
        $this->userimport = new UserImport($this->modx);

        // Add custom css file to manager-page header
        $cssFile = $this->userimport->config['cssUrl'] . 'mgr23.css';
        $this->addCss($cssFile);

        // initialize UserImport Js
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/userimport.js');

        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('user,userimport:default');
    }

    public function process(array $scriptProperties = array())
    {
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('userimport');
    }

    public function getTemplateFile()
    {
        return '';
    }

    public function checkPermissions()
    {
        return true;
    }

    public function loadCustomCssJs()
    {
        // load utilities and reusable functions
        $this->addJavascript($this->userimport->config['jsUrl'] . 'utils/utilities.js');

        // load widgets
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/import.panel.js');
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/notificationtemplate.panel.js');
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/about.panel.js');

        // load import panel widgets container
        $this->addLastJavascript($this->userimport->config['jsUrl'] . 'mgr/sections/index.panel.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function(){
            UserImport.config = ' . $this->modx->toJSON($this->userimport->config) . ';
            UserImport.request = ' . $this->modx->toJSON($_GET) . ';
            Ext.onReady(function(){MODx.add("userimport-panel-index");});
        });
        </script>');
    }
}

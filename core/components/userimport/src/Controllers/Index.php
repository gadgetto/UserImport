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

namespace Bitego\UserImport\Controllers;

use MODX\Revolution\modX;
use Bitego\UserImport\Controllers\Base;

/**
 * Index manager controller class.
 *
 * @param \MODX\Revolution\modX &$modx A reference to the modX object
 * @param array $config An array of configuration options
 * @extends Bitego\UserImport\Controllers\Base
 * @package userimport
 * @subpackage controllers
 */
class Index extends Base
{
    /**
     * {@inheritDoc}
     *
     * @access public
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/import.panel.js');
        if ($this->userimport->config['goodNewsAddOn']) {
            $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/goodnews.panel.js');
        }
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/notificationtemplate.panel.js');
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/widgets/about.panel.js');
        $this->addLastJavascript($this->userimport->config['jsUrl'] . 'mgr/sections/index.panel.js');
        $this->addHtml(
            '<script>
            Ext.onReady(function(){
                UserImport.config = ' . $this->modx->toJSON($this->userimport->config) . ';
                UserImport.request = ' . $this->modx->toJSON($_GET) . ';
                MODx.add("userimport-panel-index");
            });
            </script>'
        );
    }
}

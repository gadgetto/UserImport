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
 * UserImport main class
 *
 * @package userimport
 */

class UserImport {

    const VERSION = '1.0.0';
    const RELEASE = 'beta3';

    /** @var modX A reference to the modX object */
    public $modx = null;
    
    /** @var array $config UserImport config array */
    public $config = array();
    
    /**
     * Constructor for UserImport object
     *
     * @param modX &$modx A reference to the modX object
     * @param array $config An array of configuration options
     */
    function __construct(modX &$modx, array $config = array()) {
        $this->modx = &$modx;
 
        $corePath = $this->modx->getOption('userimport.core_path', $config, $this->modx->getOption('core_path').'components/userimport/');
        $assetsUrl = $this->modx->getOption('userimport.assets_url', $config, $this->modx->getOption('assets_url').'components/userimport/');

        $this->config = array_merge(array(
            'corePath'         => $corePath,
            'modelPath'        => $corePath.'model/',
            'processorsPath'   => $corePath.'processors/',
            'docsPath'         => $corePath.'docs/',
            'assetsUrl'        => $assetsUrl,
            'jsUrl'            => $assetsUrl.'js/',
            'cssUrl'           => $assetsUrl.'css/',
            'connectorUrl'     => $assetsUrl.'connector.php',
            'helpUrl'          => 'http://www.bitego.com/extras/userimport/',
            'componentName'    => 'UserImport',
            'componentVersion' => self::VERSION,
            'componentRelease' => self::RELEASE,
            'developerName'    => 'bitego (Martin Gartner)',
            'developerUrl'     => 'http://www.bitego.com',
        ), $config);
    }
}

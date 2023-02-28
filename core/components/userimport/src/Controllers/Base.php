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
use MODX\Revolution\modExtraManagerController;

/**
 * Base manager controller class.
 *
 * @param \MODX\Revolution\modX &$modx A reference to the modX object
 * @param array $config An array of configuration options
 * @extends MODX\Revolution\modExtraManagerController
 * @package userimport
 * @subpackage controllers
 */
class Base extends modExtraManagerController
{
    /** @var UserImport $userimport */
    public $userimport = null;

    /**
     * {@inheritDoc}
     *
     * @access public
     * @return void
     */
    public function __construct(modX $modx, $config = [])
    {
        parent::__construct($modx, $config);
        $this->userimport = $this->modx->services->get('userimport');
    }

    /**
     * {@inheritDoc}
     *
     * @access public
     * @return mixed
     */
    public function initialize()
    {
        $this->addCss($this->userimport->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->userimport->config['jsUrl'] . 'mgr/userimport.js');
        $this->addJavascript($this->userimport->config['jsUrl'] . 'utils/utilities.js');
        parent::initialize();
    }

    /**
     * {@inheritDoc}
     *
     * @access public
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['user', 'userimport:default'];
    }

    /**
     * {@inheritDoc}
     *
     * @access public
     * @returns array
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('userimport');
    }

    /**
     * {@inheritDoc}
     *
     * @access public
     * @returns string
     */
    public function getTemplateFile()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     *
     * @access public
     * @returns boolean
     */
    public function checkPermissions()
    {
        return true;
    }
}

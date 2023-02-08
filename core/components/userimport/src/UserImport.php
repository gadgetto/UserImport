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

namespace Bitego\UserImport;

use MODX\Revolution\modX;

/**
 * UserImport main class
 *
 * @package userimport
 */

class UserImport
{
    public const NAME     = 'UserImport';
    public const VERSION  = '2.0.0';
    public const RELEASE  = 'alpha1';

    public const HELP_URL = 'https://docs.bitego.com/user-import/user-guide/';
    public const DEV_NAME = 'bitego (Martin Gartner, Franz Gallei)';
    public const DEV_URL  = 'http://www.bitego.com';

    public const MIN_PHP_VERSION = '7.2.5';
    public const MIN_MODX_VERSION = '3.0.0';
    public const MAX_MODX_VERSION = '';

    /** @var \MODX\Revolution\modX A reference to the modX object */
    public $modx = null;

    /** @var array $config UserImport config array */
    public $config = [];

    /** @var boolean $debug Debug mode on/off */
    public $debug = false;

    /**
     * Constructor for UserImport object
     *
     * @param \MODX\Revolution\modX &$modx A reference to the modX object
     * @param array $config An array of configuration options
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption(
            'userimport.core_path',
            $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userimport/'
        );
        $assetsPath = $this->modx->getOption(
            'userimport.assets_path',
            $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/userimport/'
        );
        $assetsUrl = $this->modx->getOption(
            'userimport.assets_url',
            $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/userimport/'
        );

        $this->modx->lexicon->load('user,userimport:default');

        $this->config = array_merge([
            'corePath'         => $corePath,
            'srcPath'          => $corePath . 'src/',
            'modelPath'        => $corePath . 'src/Model/',
            'processorsPath'   => $corePath . 'src/Processors/',
            'chunksPath'       => $corePath . 'elements/chunks/',
            'includesPath'     => $corePath . 'includes/',
            'assetsPath'       => $assetsPath,
            'assetsUrl'        => $assetsUrl,
            'jsUrl'            => $assetsUrl . 'js/',
            'cssUrl'           => $assetsUrl . 'css/',
            'imgUrl'           => $assetsUrl . 'img/',
            'connectorUrl'     => $assetsUrl . 'connector.php',
            'helpUrl'          => self::HELP_URL,
            'componentName'    => self::NAME,
            'componentVersion' => self::VERSION,
            'componentRelease' => self::RELEASE,
            'developerName'    => self::DEV_NAME,
            'developerUrl'     => self::DEV_URL,
            'debug'            => $this->debug,
        ], $config);
    }
}

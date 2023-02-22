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
 * UserImport main class
 *
 * @package userimport
 */

class UserImport
{
    public const VERSION = '1.2.0';
    public const RELEASE = 'pl';

    /** @var modX A reference to the modX object */
    public $modx = null;

    /** @var array $config UserImport config array */
    public $config = [];

    /**
     * Constructor for UserImport object.
     *
     * @param modX &$modx A reference to the modX object
     * @param array $config An array of configuration options
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;
        $corePath = $this->modx->getOption(
            'userimport.core_path',
            $config,
            $this->modx->getOption('core_path') . 'components/userimport/'
        );
        $assetsUrl = $this->modx->getOption(
            'userimport.assets_url',
            $config,
            $this->modx->getOption('assets_url') . 'components/userimport/'
        );

        $this->config = array_merge([
            'corePath'         => $corePath,
            'modelPath'        => $corePath . 'model/',
            'processorsPath'   => $corePath . 'processors/',
            'docsPath'         => $corePath . 'docs/',
            'assetsUrl'        => $assetsUrl,
            'jsUrl'            => $assetsUrl . 'js/',
            'cssUrl'           => $assetsUrl . 'css/',
            'connectorUrl'     => $assetsUrl . 'connector.php',
            'helpUrl'          => 'http://www.bitego.com/extras/userimport/',
            'componentName'    => 'UserImport',
            'componentVersion' => self::VERSION,
            'componentRelease' => self::RELEASE,
            'developerName'    => 'bitego (Martin Gartner)',
            'developerUrl'     => 'http://www.bitego.com',
        ], $config);
    }
}

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
 * UserImport Connector
 *
 * @package userimport
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('userimport.core_path', null, $modx->getOption('core_path') . 'components/userimport/');
require_once $corePath . 'model/userimport/userimport.class.php';
$modx->userimport = new UserImport($modx);

$modx->lexicon->load('user', 'userimport:default');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->userimport->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));

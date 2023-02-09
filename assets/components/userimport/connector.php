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
 * @var \MODX\Revolution\modX $modx
 * @var Bitego\UserImport\UserImport $userimport
 * @package userimport
 */

require_once dirname(__DIR__, 3) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$userimport = $modx->services->get('userimport');
$modx->lexicon->load('userimport:default');

$modx->request->handleRequest([
    'processors_path' => $userimport->config['processorsPath'],
    'location' => '',
]);

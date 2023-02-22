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
 * Adds modMenus into package
 *
 * @package userimport
 * @subpackage build
 */

// Create menu [MODX 2.3+ method]
/*
Note: will route to the first found of the following:
[namespace-path]controllers/[manager-theme]/index.class.php
[namespace-path]controllers/default/index.class.php
[namespace-path]controllers/index.class.php
*/

$menus = [];
$menus[0] = $modx->newObject('modMenu');
$menus[0]->fromArray([
    'text'        => 'userimport',
    'parent'      => 'components',
    'description' => 'userimport.desc',
    'icon'        => '',
    'menuindex'   => 98,
    'params'      => '',
    'handler'     => '',
    'action'      => 'index',
    'namespace'   => 'userimport',
], '', true, true);

return $menus;

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
 * Adds modEvent (system events) into package
 *
 * @package userimport
 * @subpackage build
 */

$events = [];
$events[0] = $modx->newObject('modEvent');
$events[0]->fromArray([
    'name' => 'onBeforeUserImport',
    'service' => 6,
    'groupname' => 'userImport',
], '', true, true);

$events[1] = $modx->newObject('modEvent');
$events[1]->fromArray([
    'name' => 'onAfterUserImport',
    'service' => 6,
    'groupname' => 'userImport',
], '', true, true);

return $events;

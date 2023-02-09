<?php

/**
 * This file is part of the GoodNews package.
 *
 * @copyright bitego (Martin Gartner)
 * @license GNU General Public License v2.0 (and later)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use MODX\Revolution\modEvent;

/**
 * Adds custom system events (modEvent) into package
 *
 * @var modX $modx
 * @var array $sysevents
 *
 * @package userimport
 * @subpackage build
 */

$sysevents = [];
$i = 0;

$sysevents[++$i] = $modx->newObject(modEvent::class);
$sysevents[$i]->fromArray([
    'name' => 'onBeforeUserImport',
    'service' => 6,
    'groupname' => 'UserImport',
], '', true, true);

$sysevents[++$i] = $modx->newObject(modEvent::class);
$sysevents[$i]->fromArray([
    'name' => 'onAfterUserImport',
    'service' => 6,
    'groupname' => 'UserImport',
], '', true, true);

unset($i);
return $sysevents;

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
 * Adds modEvent into package
 *
 * @package userimport
 * @subpackage build
 */

$events = array();
$events[0]= $modx->newObject('modEvent');
$events[0]->fromArray(array (
    'name' => 'onBeforeUserImport',
    'service' => 6,
    'groupname' => 'userImport',
), '', true, true);

$events[1]= $modx->newObject('modEvent');
$events[1]->fromArray(array (
    'name' => 'onAfterUserImport',
    'service' => 6,
    'groupname' => 'userImport',
), '', true, true);
return $events;


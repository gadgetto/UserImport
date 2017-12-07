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
 * Add system settings to package.
 *
 * @package userimport
 * @subpackage build
 */

$settings = array();

$settings['userimport.delimiter'] = $modx->newObject('modSystemSetting');
$settings['userimport.delimiter']->fromArray(array(
    'key'       => 'userimport.delimiter',
    'value'     => ',',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$settings['userimport.enclosure'] = $modx->newObject('modSystemSetting');
$settings['userimport.enclosure']->fromArray(array(
    'key'       => 'userimport.enclosure',
    'value'     => '"',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$settings['userimport.autousername'] = $modx->newObject('modSystemSetting');
$settings['userimport.autousername']->fromArray(array(
    'key'       => 'userimport.autousername',
    'value'     => '0',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$settings['userimport.setimportmarker'] = $modx->newObject('modSystemSetting');
$settings['userimport.setimportmarker']->fromArray(array(
    'key'       => 'userimport.setimportmarker',
    'value'     => '1',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$settings['userimport.notifyusers'] = $modx->newObject('modSystemSetting');
$settings['userimport.notifyusers']->fromArray(array(
    'key'       => 'userimport.notifyusers',
    'value'     => '0',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$settings['userimport.mailsubject'] = $modx->newObject('modSystemSetting');
$settings['userimport.mailsubject']->fromArray(array(
    'key'       => 'userimport.mailsubject',
    'value'     => 'Notification: Your New User Account!',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

$mailbody = <<<EOT
Hello,<br>
<br>
we\'d like to inform you, that your user credentials were imported into our system and a new user account was created for you!<br> 
<br>
<strong>Here are your new account credentials:</strong><br>
<br>
Name: [[+fullname]]<br>
Email: [[+email]]<br>
Your username: [[+username]]<br>
Your password: [[+password]]<br>
<br>
If you have any questions please don\'t hesitate to contact us!<br>
<br>
Your [[++site_name]] team
EOT;

$settings['userimport.mailbody'] = $modx->newObject('modSystemSetting');
$settings['userimport.mailbody']->fromArray(array(
    'key'       => 'userimport.mailbody',
    'value'     => $mailbody,
    'xtype'     => 'textarea',
    'namespace' => 'userimport',
    'area'      => '',
), '', true, true);

return $settings;

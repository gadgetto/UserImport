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

use MODX\Revolution\modSystemSetting;

/**
 * Add system settings to package.
 *
 * @var modX $modx
 * @var array $settings
 *
 * @package userimport
 * @subpackage build
 */

$settings = [];

$settings['userimport.delimiter'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.delimiter']->fromArray([
    'key'       => 'userimport.delimiter',
    'value'     => ',',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

$settings['userimport.enclosure'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.enclosure']->fromArray([
    'key'       => 'userimport.enclosure',
    'value'     => '"',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

$settings['userimport.autousername'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.autousername']->fromArray([
    'key'       => 'userimport.autousername',
    'value'     => '0',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

$settings['userimport.setimportmarker'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.setimportmarker']->fromArray([
    'key'       => 'userimport.setimportmarker',
    'value'     => '1',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

$settings['userimport.notifyusers'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.notifyusers']->fromArray([
    'key'       => 'userimport.notifyusers',
    'value'     => '0',
    'xtype'     => 'combo-boolean',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

$settings['userimport.mailsubject'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.mailsubject']->fromArray([
    'key'       => 'userimport.mailsubject',
    'value'     => 'Notification: Your New User Account!',
    'xtype'     => 'textfield',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

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

$settings['userimport.mailbody'] = $modx->newObject(modSystemSetting::class);
$settings['userimport.mailbody']->fromArray([
    'key'       => 'userimport.mailbody',
    'value'     => $mailbody,
    'xtype'     => 'textarea',
    'namespace' => 'userimport',
    'area'      => '',
], '', true, true);

unset($mailbody);
return $settings;

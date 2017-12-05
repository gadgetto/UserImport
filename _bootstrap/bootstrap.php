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
 * Bootstrap script for UserImport development environment.
 *
 * @package userimport
 * @subpackage bootstrap
 */

// Define package name
define('PKG_NAME', 'UserImport');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));

// Define paths
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root'           => $root,
    'build'          => $root.'_build/',
    'includes'       => $root.'_build/includes/',
    'data'           => $root.'_build/data/',
    'events'         => $root.'_build/data/events/',    
    'properties'     => $root.'_build/properties/',
    'resolvers'      => $root.'_build/resolvers/',
    'packages'       => $root.'_packages/',
    'chunks'         => $root.'core/components/'.PKG_NAME_LOWER.'/elements/chunks/',
    'plugins'        => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
    'resources'      => $root.'core/components/'.PKG_NAME_LOWER.'/elements/resources/',
    'snippets'       => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
    'templates'      => $root.'core/components/'.PKG_NAME_LOWER.'/elements/templates/',
    'lexicon'        => $root.'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs'           => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'source_core'    => $root.'core/components/'.PKG_NAME_LOWER,
    'source_assets'  => $root.'assets/components/'.PKG_NAME_LOWER,
);
unset($root);

echo '<pre>';

if (!file_exists($sources['root'].'config.core.php')) {
    die('ERROR: missing '.$sources['root'].'config.core.php file.');
}

require_once $sources['root'].'config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->getService('error','error.modError', '', '');

$modelPath = $modx->getOption('userimport.core_path', null, $modx->getOption('core_path').'components/userimport/').'model/';
$modx->addPackage('userimport', $modelPath);

$manager = $modx->getManager();
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

// Databes tables
$objects = array(
    //'TabelName1',
    //'TabelName2',
    //'TabelName3',
);

if (!empty($objects)) {
    $modx->log(modX::LOG_LEVEL_INFO, "Creating database tables...");
    $count = 0;
    foreach ($objects as $obj) {
        $manager->createObjectContainer($obj);
        $count++;
    }
    $modx->log(modX::LOG_LEVEL_INFO, "{$count} Database tables created.");
    flush();
}
unset($objects, $obj, $count);

// System settings
$settings = include $sources['data'].'transport.settings.php';

if (!empty($settings)) {
    $modx->log(modX::LOG_LEVEL_INFO, "Creating system settings...");
    $count = 0;
    foreach ($settings as $setting) {
        $setting->save();
        $count++;
    }
    $cacheRefreshOptions = array('system_settings' => array());
    $modx->cacheManager->refresh($cacheRefreshOptions);
    $modx->log(modX::LOG_LEVEL_INFO, "{$count} System settings created.");
    flush();
}
unset($settings, $setting, $cacheRefreshOptions);

$modx->log(modX::LOG_LEVEL_INFO, 'Bootstrap script finished.');

echo '</pre>';

flush();
exit();
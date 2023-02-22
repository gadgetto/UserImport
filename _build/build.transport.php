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
 * Build script for UserImport transport package
 * (supports MODX version 2.3.x up to 2.8.x)
 *
 * @package userimport
 * @subpackage build
 */

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Define package name */
define('PKG_NAME', 'UserImport');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));

/* Define build paths */
$root = dirname(dirname(__FILE__)) . '/';
$sources = [
    'root'           => $root,
    'build'          => $root . '_build/',
    'includes'       => $root . '_build/includes/',
    'data'           => $root . '_build/data/',
    'events'         => $root . '_build/data/events/',
    'properties'     => $root . '_build/properties/',
    'resolvers'      => $root . '_build/resolvers/',
    'packages'       => $root . '_packages/',
    'chunks'         => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'plugins'        => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'resources'      => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/resources/',
    'snippets'       => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'templates'      => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/templates/',
    'lexicon'        => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs'           => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'source_core'    => $root . 'core/components/' . PKG_NAME_LOWER . '/',
    'source_assets'  => $root . 'assets/components/' . PKG_NAME_LOWER . '/',
];
unset($root);

require_once $sources['root'] . 'config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/* Connect to MODx */
$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
echo '<pre>';
flush();

/* Add UserImport package to get VERSION and RELEASE */
$corePath = $modx->getOption('userimport.core_path', null, $modx->getOption('core_path') . 'components/userimport/');
$userimport = $modx->getService('userimport', 'UserImport', $corePath . 'model/userimport/');
if (!($userimport instanceof UserImport)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'UserImport class could not be loaded.');
    exit();
}

/* Define package version and release */
define('PKG_VERSION', UserImport::VERSION);
define('PKG_RELEASE', UserImport::RELEASE);

/* Prepare Transport Package */
$modx->log(
    modX::LOG_LEVEL_INFO,
    'Building transport package for <b>' . PKG_NAME_LOWER . '-' . PKG_VERSION . '-' . PKG_RELEASE . '</b>...'
);

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->directory = $sources['packages'];
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(
    PKG_NAME_LOWER,
    false,
    true,
    '{core_path}components/' . PKG_NAME_LOWER . '/',
    '{assets_path}components/' . PKG_NAME_LOWER . '/'
);
$modx->log(modX::LOG_LEVEL_INFO, 'Prepared Transport Package and registered Namespace.');
flush();

/* Add menus */
$menus = include $sources['data'] . 'transport.menus.php';
if (!empty($menus) && is_array($menus)) {
    foreach ($menus as $menu) {
        $vehicle = $builder->createVehicle($menu, [
            xPDOTransport::UNIQUE_KEY => 'text',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ]);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in <b>' . count($menus) . '</b> menu(s).');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in menu(s). Data missing.');
}
flush();
unset($vehicle, $menus, $menu);

/* Add system settings */
$settings = include $sources['data'] . 'transport.settings.php';
if (!empty($settings) && is_array($settings)) {
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, [
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false, // existing settings should not be overwritten
        ]);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in <b>' . count($settings) . '</b> system setting(s).');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in system setting(s). Data missing.');
}
flush();
unset($vehicle, $settings, $setting);

/* Add system events */
$sysevents = include $sources['data'] . 'transport.sysevents.php';
if (!empty($sysevents) && is_array($sysevents)) {
    foreach ($sysevents as $sysevent) {
        $vehicle = $builder->createVehicle($sysevent, [
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ]);
        $builder->putVehicle($vehicle);
    }
    $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in <b>' . count($sysevents) . ' </b> system events.');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in system system event(s). Data missing.');
}
flush();
unset($sysevents, $sysevent);

/* Create elements category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$category->set('parent', 0);
$modx->log(modX::LOG_LEVEL_INFO, 'Created default elements category.');
flush();

/* Create category vehicle for all elements */
$attributes = [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'Snippets' => [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
        ],
        'Chunks' => [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
        ],
        'Plugins' => [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
                'PluginEvents' => [
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                    xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
                ],
            ],
        ],
        'Templates' => [
            xPDOTransport::UNIQUE_KEY => 'templatename',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
        ],
        'TemplateVars' => [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
        ],
    ],
    xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
];

$modx->log(modX::LOG_LEVEL_INFO, 'Adding category vehicle for all elements...');

// Exclude files with a specific pattern (eg. __ prefix)
$categoryAttributes = array_merge($attributes, ['copy_exclude_patterns' => ['/^__/']]);
$vehicle = $builder->createVehicle($category, $categoryAttributes);
flush();
// Don't unset $vehicle as we still need it to add resolvers and validators!
unset($category, $attributes, $categoryAttributes);

/* Add file resolvers */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers...');
$vehicle->resolve('file', [
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);
$vehicle->resolve('file', [
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
]);

$builder->putVehicle($vehicle);
flush();
unset($vehicle);

/* Add the license file, readme and setup options */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options...');
$builder->setPackageAttributes([
    'license'   => file_get_contents($sources['docs'] . 'license.txt'),
    'readme'    => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'copy_exclude_patterns' => ['/^__/'],
    //'setup-options' => [
    //    'source' => $sources['build'].'setup.options.php',
    //],
]);

/* Create zip package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing transport package zip...');
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, 'Transport Package Built.');
$modx->log(modX::LOG_LEVEL_INFO, 'Execution time: ' . $totalTime);
flush();

exit();

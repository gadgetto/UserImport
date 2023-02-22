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
 * Bootstrap script for setting up UserImport development environment
 * (supports MODX version 2.3.x up to 2.8.x)
 *
 * @package userimport
 * @subpackage bootstrap
 */

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Define package name and namespace */
define('PKG_NAME', 'UserImport');
define('PKG_NAMESPACE', strtolower(PKG_NAME));
define('MIN_MODX_VERSION', '2.3.0');
define('MAX_MODX_VERSION', '2.8.99');
define('MIN_PHP_VERSION', '7.0.0');

/* Define paths */
$root = dirname(__DIR__, 1) . '/';
$sources = [
    'root'           => $root,
    'build'          => $root . '_build/',
    'build_data'     => $root . '_build/data/',
    'includes'       => $root . '_bootstrap/includes/',
    'lexicon'        => $root . 'core/components/' . PKG_NAMESPACE . '/lexicon/',
    'docs'           => $root . 'core/components/' . PKG_NAMESPACE . '/docs/',
    'source_core'    => $root . 'core/components/' . PKG_NAMESPACE . '/',
    'source_assets'  => $root . 'assets/components/' . PKG_NAMESPACE . '/',
];
unset($root);

if (!file_exists($sources['root'] . 'config.core.php')) {
    die('ERROR: missing ' . $sources['root'] . 'config.core.php file.');
}

require_once $sources['root'] . 'config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['includes'] . 'functions.php';

/* Load MODX */
$modx = new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError', '', '');
$manager = $modx->getManager();
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
echo '<pre>';
flush();

$modx->log(
    modX::LOG_LEVEL_INFO,
    'Building development environment for <b>' . PKG_NAME . '</b>...'
);

/* Get MODX version, eg. '2.8.4-pl' */
$modXversion = $modx->getVersionData();
$modXversion = $modXversion['full_version'];
$modx->log(modX::LOG_LEVEL_INFO, 'MODX version: ' . $modXversion);

/* Check if package is already installed */
if (isTransportPackageInstalled($modx, PKG_NAME)) {
    $modx->log(modX::LOG_LEVEL_WARN, PKG_NAME . ' is installed on this system.');
    $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment stopped!');
    flush();
    exit();
}

/* Check if development environment for package is already available */
if (existsNamespace($modx, PKG_NAMESPACE)) {
    $modx->log(
        modX::LOG_LEVEL_WARN,
        'It seems, that a development environment for ' . PKG_NAME .
        ' is already available on this system!'
    );
    $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment stopped!');
    flush();
    exit();
}

/* Check system requirements */
$modx->log(modX::LOG_LEVEL_WARN, 'Checking if system meets minimum requirements...');
$success = true;

/* Check min/max MODX version */
if (!empty(MIN_MODX_VERSION)) {
    $level = modX::LOG_LEVEL_INFO;
    if (version_compare($modXversion, MIN_MODX_VERSION, '<=')) {
        $level = modX::LOG_LEVEL_ERROR;
        $success = false;
    }
    $modx->log(
        $level,
        '-> min. required MODX Revo version: ' . MIN_MODX_VERSION .
        ' -- found: <b>' . $modXversion . '</b>'
    );
    if (!$success) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment stopped!');
        flush();
        exit();
    }
}
if (!empty(MAX_MODX_VERSION)) {
    $level = modX::LOG_LEVEL_INFO;
    if (version_compare($modXversion, MAX_MODX_VERSION, '>=')) {
        $level = modX::LOG_LEVEL_ERROR;
        $success = false;
    }
    $modx->log(
        $level,
        '-> max. required MODX Revo version: ' . MAX_MODX_VERSION .
        ' -- found: <b>' . $modXversion . '</b>'
    );
    if (!$success) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment stopped!');
        flush();
        exit();
    }
}

/* Check PHP version */
if (!empty(MIN_PHP_VERSION)) {
    $level = modX::LOG_LEVEL_INFO;
    if (version_compare(PHP_VERSION, MIN_PHP_VERSION, '<=')) {
        $level = modX::LOG_LEVEL_ERROR;
        $success = false;
    }
    $modx->log(
        $level,
        '-> min. required PHP version: ' . MIN_PHP_VERSION .
        ' -- found: <b>' . PHP_VERSION . '</b>'
    );
    if (!$success) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment stopped!');
        flush();
        exit();
    }
}
flush();
unset($success, $level);

/* Create namespace */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding namespace...');
if (
    createObject(
        $modx,
        'modNamespace',
        [
            'name' => PKG_NAMESPACE,
            'path' => $sources['source_core'],
            'assets_path' => $sources['source_assets'],
        ],
        'name',
        false
    )
) {
    $modx->log(modX::LOG_LEVEL_INFO, '-> added namespace: ' . PKG_NAMESPACE);
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '-> namespace ' . PKG_NAMESPACE . ' could not be added.');
    $modx->log(modX::LOG_LEVEL_INFO, 'Building development environment failed!');
    flush();
    exit();
}
flush();

/* Add menus (using sources from _build/data/ directory) */
$menus = include $sources['build_data'] . 'transport.menus.php';
$modx->log(modX::LOG_LEVEL_INFO, 'Adding menu entries...');
if (!empty($menus) && is_array($menus)) {
    foreach ($menus as $menu) {
        if ($menu->save()) {
            $modx->log(modX::LOG_LEVEL_INFO, '-> added menu entry');
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '-> menu entry could not be added. Saving failed!');
        }
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Menu entries could not be added. Data missing.');
}
flush();
unset($menus, $menu);

/* Add system settings (using sources from _build/data/ directory) */
$settings = include $sources['build_data'] . 'transport.settings.php';
$modx->log(modX::LOG_LEVEL_INFO, 'Adding system settings...');
if (!empty($settings) && is_array($settings)) {
    foreach ($settings as $setting => $obj) {
        if ($obj->save()) {
            $modx->log(modX::LOG_LEVEL_INFO, '-> added system setting: ' . $setting);
        } else {
            $modx->log(
                modX::LOG_LEVEL_ERROR,
                '-> system setting ' . $setting . ' could not be added. Saving failed!'
            );
        }
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'System settings could not be added. Data missing.');
}
flush();
unset($settings, $setting, $obj);

/* Create default elements category */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding default elements category...');
if (
    createObject(
        $modx,
        'modCategory',
        [
            'category' => PKG_NAME,
            'parent' => 0,
        ],
        'category',
        false
    )
) {
    $modx->log(modX::LOG_LEVEL_INFO, '-> added default elements category: ' . PKG_NAME);
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '-> default elements category ' . PKG_NAME . ' could not be added.');
}

/* Get ID of default elements category for later use */
$defaultCategoryId = 0;
/** @var modCategory $obj */
$obj = $modx->getObject('modCategory', ['category' => PKG_NAME]);
if ($obj) {
    $defaultCategoryId = $obj->get('id');
}
flush();
unset($obj);

/* Add system events (using sources from _build/data/ directory) */
$sysevents = include $sources['build_data'] . 'transport.sysevents.php';
$modx->log(modX::LOG_LEVEL_INFO, 'Adding system events...');
if (!empty($sysevents) && is_array($sysevents)) {
    foreach ($sysevents as $event) {
        $eventName = $event->get('name');
        if ($event->save()) {
            $modx->log(modX::LOG_LEVEL_INFO, '-> added system event: ' . $eventName);
        } else {
            $modx->log(
                modX::LOG_LEVEL_ERROR,
                '-> system event ' . $eventName . ' could not be added. Saving failed!'
            );
        }
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'System events could not be added. Data missing.');
}
flush();
unset($sysevents, $event, $eventName);

/**
 * The following parts are equivalent to the resolvers/validators of build script
 */

/* Add development path settings */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding development path settings...');
if (createSystemSetting($modx, 'core_path', $sources['source_core'], PKG_NAMESPACE)) {
    $modx->log(modX::LOG_LEVEL_INFO, '-> added path setting: ' . PKG_NAMESPACE . '.core_path');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '-> path setting ' . PKG_NAMESPACE . '.core_path could not be added.');
}

if (createSystemSetting($modx, 'assets_path', $sources['source_assets'], PKG_NAMESPACE)) {
    $modx->log(modX::LOG_LEVEL_INFO, '-> added path setting: ' . PKG_NAMESPACE . '.assets_path');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '-> path setting ' . PKG_NAMESPACE . '.assets_path could not be added.');
}

if (createSystemSetting($modx, 'assets_url', fetchAssetsUrl(PKG_NAMESPACE), PKG_NAMESPACE)) {
    $modx->log(modX::LOG_LEVEL_INFO, '-> added path setting: ' . PKG_NAMESPACE . '.assets_url');
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '-> path setting ' . PKG_NAMESPACE . '.assets_url could not be added.');
}
flush();

/* Clear the MODX cache */
$modx->cacheManager->refresh();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, 'Building development environment finished!');
$modx->log(modX::LOG_LEVEL_INFO, 'Execution time: ' . $totalTime);
echo '</pre>';
flush();
exit();

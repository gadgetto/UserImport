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
 * Helper functions for _bootstrap
 *
 * @package userimport
 * @subpackage bootstrap
 */

/**
 * Creates an object.
 *
 * @param mixed &$modx
 * @param string $className
 * @param array $data
 * @param string $primaryField
 * @param bool $update
 * @return bool
 */
function createObject(&$modx, $className = '', array $data = [], $primaryField = '', $update = true)
{
    /* @var xPDOObject $object */
    $object = null;

    /* Attempt to get the existing object */
    if (!empty($primaryField)) {
        if (is_array($primaryField)) {
            $condition = [];
            foreach ($primaryField as $key) {
                $condition[$key] = $data[$key];
            }
        } else {
            $condition = [$primaryField => $data[$primaryField]];
        }

        $object = $modx->getObject($className, $condition);
        if ($object instanceof $className) {
            if ($update) {
                $object->fromArray($data);
                return $object->save();
            } else {
                $condition = $modx->toJSON($condition);
                $modx->log(
                    modX::LOG_LEVEL_INFO,
                    "-> skipping object {$className} {$condition}. Already exists!"
                );
                return true;
            }
        }
    }

    /* Create new object if it doesn't exist */
    if (!$object) {
        $object = $modx->newObject($className);
        $object->fromArray($data, '', true);
        return $object->save();
    }

    return false;
}

/**
 * Create a system setting.
 *
 * @param mixed &$modx
 * @param mixed $key
 * @param mixed $value
 * @param string $xtype
 * @param string $namespace
 * @return boolean
 */
function createSystemSetting(&$modx, $key, $value, $namespace, $xtype = 'textfield', $area = 'Development')
{
    $exists = $modx->getCount('modSystemSetting', ['key' => "{$namespace}.{$key}"]);
    $saved = false;
    if (!$exists) {
        $setting = $modx->newObject('modSystemSetting');
        $setting->set('key', "{$namespace}.{$key}");
        $setting->set('value', $value);
        $setting->set('xtype', $xtype);
        $setting->set('namespace', $namespace);
        $setting->set('area', $area);
        $setting->set('editedon', time());
        if ($setting->save()) {
            $saved = true;
        }
    }
    return $saved;
}

/**
 * Cecks if a MODX transport package is installed.
 *
 * @param mixed &$modx
 * @param string $name Name of transport package
 * @return boolean
 */
function isTransportPackageInstalled(&$modx, $tpname)
{
    $installed = false;
    /** @var transport.modTransportPackage $package */
    $package = $modx->getObject('transport.modTransportPackage', [
        'package_name' => $tpname,
    ]);
    if (is_object($package)) {
        $installed = true;
    }
    return $installed;
}

/**
 * Cecks if a MODX namespace exists.
 *
 * @param mixed &$modx
 * @param string $name Name of namespace
 * @return boolean
 */
function existsNamespace(&$modx, $nspace)
{
    $exists = false;
    /** @var modNamespace $namespace */
    $namespace = $modx->getObject('modNamespace', ['name' => $nspace,]);
    if (is_object($namespace)) {
        $exists = true;
    }
    return $exists;
}

/**
 * Get ID of a MODX category.
 *
 * @param mixed &$modx
 * @param mixed $name
 * @return int category ID | 0 if not found
 */
function getCategoryID(&$modx, $name)
{
    $id = 0;
    $categoryObj = $modx->getObject('modCategory', ['category' => $name]);
    if (is_object($categoryObj)) {
        $id = $categoryObj->get('id');
    }
    return $id;
}

/**
 * Fetch the assets url.
 *
 * @param string $namespace
 * @return string
 */
function fetchAssetsUrl($namespace)
{
    $url = 'http';
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
        $url .= 's';
    }
    $url .= '://' . $_SERVER["SERVER_NAME"];
    if ($_SERVER['SERVER_PORT'] != '80') {
        $url .= ':' . $_SERVER['SERVER_PORT'];
    }
    $requestUri = $_SERVER['REQUEST_URI'];
    $bootstrapPos = strpos($requestUri, '_bootstrap/');
    $requestUri = rtrim(substr($requestUri, 0, $bootstrapPos), '/') . '/';

    return "{$url}{$requestUri}assets/components/" . $namespace . '/';
}

/**
 * Get content of php file.
 *
 * @param string $filename
 * @return mixed|string
 */
function getPHPFileContent($filename)
{
    $o = file_get_contents($filename);
    $o = str_replace('<?php', '', $o);
    $o = str_replace('?>', '', $o);
    $o = trim($o);
    return $o;
}

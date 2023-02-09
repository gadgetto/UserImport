<?php

use MODX\Revolution\modX;
use MODX\Revolution\Error\modError;
use Bitego\UserImport\ImportHandler;

$time_start = microtime_float();

/* Define paths */
$root = dirname(__DIR__, 1) . '/';

require_once $root . 'config.core.php';
require_once MODX_CORE_PATH . 'vendor/autoload.php';

/* Load MODX */
$modx = new modX();
$modx->initialize('mgr');
if (!$modx->services->has('error')) {
    $modx->services->add('error', function ($c) use ($modx) {
        return new modError($modx);
    });
}
$modx->error = $modx->services->get('error');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
echo '<pre>';
flush();

$userimport = $modx->services->get('userimport');
echo '<b>' . $userimport::NAME . ' ' . $userimport::VERSION . ' ' . $userimport::RELEASE . '</b><br><br>';

/********** Start test-code **********/




/********** End test-code **********/

$time_end = microtime_float();
$time = $time_end - $time_start;
echo '<br><br>';
echo 'Processing time: ' . $time;
echo '</pre>';

function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}

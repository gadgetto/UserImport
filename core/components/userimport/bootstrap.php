<?php

/**
 * Bootstrap file for MODX 3.x
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 * @see \MODX\Revolution\modX::_initNamespaces()
 */

require_once __DIR__ . '/vendor/autoload.php';

$modx->services->add('userimport', function ($c) use ($modx) {
    return new \Bitego\UserImport\UserImport($modx);
});

if (!$modx->services->has('mail')) {
    $modx->services->add('mail', function ($c) use ($modx) {
        return new \MODX\Revolution\Mail\modPHPMailer($modx);
    });
}

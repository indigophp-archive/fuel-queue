<?php
// This is global bootstrap for autoloading

$_SERVER['doc_root']     = __DIR__ . '/../../../../';
$_SERVER['app_path']     = __DIR__ . '/../../../app/';
$_SERVER['core_path']    = __DIR__ . '/../../../core/';
$_SERVER['package_path'] = __DIR__ . '/../../';
$_SERVER['vendor_path']  = __DIR__ . '/../../../vendor/';

require_once __DIR__ . '/../../../core/bootstrap_phpunit.php';
require_once __DIR__ . '/../vendor/autoload.php';

\Package::load('queue');

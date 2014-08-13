<?php
// Here you can initialize variables that will be available to your tests

$package = \Codeception\Configuration::projectDir();

\Package::load('queue', $package);

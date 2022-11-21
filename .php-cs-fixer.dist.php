<?php

use Symfony\Component\Finder\Finder;

$config = new PhpCsFixer\Config();
$rules = array(
    '@PSR12' => true,
    'visibility_required' => false, // compatible only with PHP 7.1+
);
$config->setRules($rules);

/** @var Finder $finder */
$finder = $config
    ->setUsingCache(true)
    ->getFinder();

$finder
    ->in(__DIR__ . '/src')
    ->notName('tijsverkoyen_classes.php')
    ->sortByName();

return $config;
